function ShaderX(options) {
	var defaults = {
		container     : null,
		sizeContainer : null,
		autoPlay      : true,
		vertexShader  : '',
		fragmentShader: '',
		width         : 0,
		height        : 0,
		mouseMove     : false,
		distImage     : false
	};
	this.options = jQuery.extend({}, defaults, options);
	this.container = this.options.container;
	this.pixelRatio = window.devicePixelRatio;
	this.uniforms = {};
	this.time = 0;
	this.progress = 0;
	this.empty = true;
	this.images = {};
	this.texture1 = null;
	this.texture2 = null;
	this.resizing = false;
	this.resizingTimeout = 0;
	this.border = 0;
	this.scale = 1;
	this.drawn = false;
	this.runned = false;
	this.mouseX = 0;
	this.mouseY = 0;
	this.loadedTextures = {};
	if (this.options.autoPlay) {
		this.init();
	}
}

ShaderX.prototype = {

	init: function() {
		var that = this;
		window.addEventListener('resize', function() { that.resize(); });

		if (this.options.autoPlay) {
			this.runned = true;
			this.render();
			this.raf();
		}

	},

	render: function() {

		if (!this.container.hasClass('wd-with-webgl')) {
			this.createCanvas();
			this.container.append(this.canvas);
			this.container.addClass('wd-with-webgl');
		}

		if (this.gl && ((this.progress > 0 && this.progress < 1) || !this.drawn)) {
			this.renderCanvas();
			this.drawn = true;
		}

	},

	createCanvas: function() {
		this.canvas = document.createElement('CANVAS');
		this.gl = this.canvas.getContext('webgl');

		if (!this.gl) {
			console.log('WebGL is not supported');
			return;
		}

		this.canvas.width = this.options.width * this.pixelRatio;
		this.canvas.height = this.options.height * this.pixelRatio;

		var vertexShader   = this.createShader(this.gl.VERTEX_SHADER, this.options.vertexShader),
		    fragmentShader = this.createShader(this.gl.FRAGMENT_SHADER, this.options.fragmentShader);

		this.program = this.createProgram(vertexShader, fragmentShader);

		var positionAttributeLocation = this.gl.getAttribLocation(this.program, 'a_position');

		var positionBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, positionBuffer);

		var x1 = 0;
		var x2 = this.options.width * this.pixelRatio;
		var y1 = 0;
		var y2 = this.options.height * this.pixelRatio;

		var positions = [
			x1,
			y1,
			x2,
			y1,
			x1,
			y2,
			x1,
			y2,
			x2,
			y1,
			x2,
			y2
		];

		this.gl.bufferData(this.gl.ARRAY_BUFFER, new Float32Array(positions), this.gl.STATIC_DRAW);

		// Tell Webthis.GL how to convert from clip space to pixels
		this.gl.viewport(0, 0, this.gl.canvas.width, this.gl.canvas.height);

		// Clear the canvas
		this.gl.clearColor(0, 0, 0, 0);
		this.gl.clear(this.gl.COLOR_BUFFER_BIT);

		// Tell it to use our program (pair of shaders)
		this.gl.useProgram(this.program);

		// Compute the matrices
		var projectionMatrix = [
			2 / this.gl.canvas.width,
			0,
			0,
			0,
			-2 / this.gl.canvas.height,
			0,
			-1,
			1,
			1
		];

		this.addUniform('3fv', 'u_matrix', projectionMatrix);
		this.addUniform('1f', 'u_flipY', 1);
		this.addUniform('1f', 'u_time', 0.0);
		this.addUniform('2f', 'u_pixels', [
			this.options.width * this.pixelRatio,
			this.options.height * this.pixelRatio
		]);
		this.addUniform('1f', 'u_progress', 0);
		this.addUniform('2f', 'u_resolution', [
			this.gl.canvas.width,
			this.gl.canvas.height
		]);
		this.addUniform('2f', 'u_uvRate', [
			1,
			1
		]);
		this.addUniform('1f', 'u_scale', this.scale);

		if (this.options.mouseMove) {
			this.addUniform('2f', 'u_mouse', [
				0.5,
				0
			]);
		}

		// Turn on the attribute
		this.gl.enableVertexAttribArray(positionAttributeLocation);

		// Tell the attribute how to get data out of positionBuffer (ARRAY_BUFFER)
		var size = 2;          // 2 components per iteration
		var type = this.gl.FLOAT;   // the data is 32bit floats
		var normalize = false; // don't normalize the data
		var stride = 0;        // 0 = move forward size * sizeof(type) each iteration to get the next position
		var offset = 0;        // start at the beginning of the buffer
		this.gl.vertexAttribPointer(positionAttributeLocation, size, type, normalize, stride, offset);

		var texCoordLocation = this.gl.getAttribLocation(this.program, 'a_texCoord');

		// set coordinates for the rectanthis.gle
		var texCoordBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, texCoordBuffer);
		this.gl.bufferData(this.gl.ARRAY_BUFFER, new Float32Array([
			0.0,
			0.0,
			1.0,
			0.0,
			0.0,
			1.0,
			0.0,
			1.0,
			1.0,
			0.0,
			1.0,
			1.0
		]), this.gl.STATIC_DRAW);
		this.gl.enableVertexAttribArray(texCoordLocation);
		this.gl.vertexAttribPointer(texCoordLocation, 2, this.gl.FLOAT, false, 0, 0);

		if (this.texture1) {
			this.loadImageTexture(this.texture1, 0);
		}

		if (this.options.distImage) {
			var distImage = new Image();

			this.requestCORSIfNotSameOrigin(distImage, this.options.distImage);

			distImage.src = this.options.distImage;

			var that = this;

			distImage.onload = function() {
				that.loadImageTexture(distImage, 2);
			};
		}
	},

	raf: function() {
		if (!this.canvas) {
			return;
		}

		var that = this;

		function animate() {
			that.time += 0.03;

			that.updateUniform('u_time', that.time);

			if (that.options.mouseMove) {
				var currentMouse = that.getUniform('u_mouse'),
				    currentX     = currentMouse[0],
				    currentY     = currentMouse[1];

				var newX = (!currentX) ? that.mouseX : currentX + (that.mouseX - currentX) * .05,
				    newY = (!currentY) ? that.mouseY : currentY + (that.mouseY - currentY) * .05;

				that.updateUniform('u_mouse', [
					newX,
					newY
				]);
			}

			if (that.progress < 0) {
				that.progress = 0;
			}
			if (that.progress > 1) {
				that.progress = 1;
			}

			that.updateUniform('u_progress', that.progress);

			that.updateUniform('u_scale', that.scale);

			that.render();
			that.requestID = window.requestAnimationFrame(animate);
		}

		animate();

	},

	resize: function() {

		var that = this;

		clearTimeout(this.resizingTimeout);

		this.resizingTimeout = setTimeout(function() {

			if (!that.canvas) {
				return;
			}

			var displayWidth = Math.floor(that.options.sizeContainer.outerWidth() * that.pixelRatio);
			var displayHeight = Math.floor(that.options.sizeContainer.outerHeight() * that.pixelRatio);

			if (that.gl.canvas.width !== displayWidth || that.gl.canvas.height !== displayHeight) {
				that.gl.canvas.width = displayWidth;
				that.gl.canvas.height = displayHeight;
			}

			that.updateUniform('u_resolution', [
				displayWidth,
				displayHeight
			]);
			that.updateUniform('u_pixels', [
				displayWidth,
				displayHeight
			]);
			that.updateUniform('u_uvRate', [
				1,
				displayHeight / displayWidth
			]);

			that.gl.viewport(0, 0, displayWidth, displayHeight);
			that.drawn = false;

		}, 500);
	},

	run: function() {
		if (this.runned) {
			return;
		}
		this.runned = true;
		this.render();
		this.raf();
	},

	stop: function() {
		if (!this.runned) {
			return;
		}
		window.cancelAnimationFrame(this.requestID);
		this.destroyCanvas();
		this.container.find('canvas').remove();
		this.container.removeClass('wd-with-webgl');
		this.runned = false;
	},

	renderCanvas: function() {

		if (this.empty) {
			return false;
		}

		// this.gl.clear(this.gl.COLOR_BUFFER_BIT | this.gl.DEPTH_BUFFER_BIT);
		this.gl.drawArrays(this.gl.TRIANGLES, 0, 6);
	},

	destroyCanvas: function() {

		if (!this.gl) {
			return;
		}

		this.canvas = null;
		this.gl.getExtension('WEBGL_lose_context').loseContext();
		this.gl = null;
	},

	createShader: function(type, source) {
		var shader = this.gl.createShader(type);
		this.gl.shaderSource(shader, source);
		this.gl.compileShader(shader);
		var success = this.gl.getShaderParameter(shader, this.gl.COMPILE_STATUS);

		if (success) {
			return shader;
		}

		console.log(this.gl.getShaderInfoLog(shader));
		this.gl.deleteShader(shader);
	},

	createProgram: function(vertexShader, fragmentShader) {
		var program = this.gl.createProgram();
		this.gl.attachShader(program, vertexShader);
		this.gl.attachShader(program, fragmentShader);
		this.gl.linkProgram(program);
		var success = this.gl.getProgramParameter(program, this.gl.LINK_STATUS);

		if (success) {
			return program;
		}

		console.log(this.gl.getProgramInfoLog(program));
		this.gl.deleteProgram(program);
	},

	addUniform: function(type, name, value) {
		var location = this.gl.getUniformLocation(this.program, name);

		this.uniforms[name] = {
			location: location,
			type    : type
		};

		if (value !== false) {
			this.updateUniform(name, value);
		}

	},

	updateUniform: function(name, value) {
		if (!this.gl) {
			return;
		}

		var uniform = this.uniforms[name];

		switch (uniform.type) {
			case '1f':
				this.gl.uniform1f(uniform.location, value);
				break;
			case '2f':
				this.gl.uniform2f(uniform.location, value[0], value[1]);
				break;
			case '1i':
				this.gl.uniform1i(uniform.location, value);
				break;
			case '3fv':
				this.gl.uniformMatrix3fv(uniform.location, false, value);
				break;
		}
	},

	getUniform: function(name, value) {
		if (!this.gl) {
			return;
		}

		var uniform = this.uniforms[name];

		return this.gl.getUniform(this.program, uniform.location);
	},

	getImageId: function(src) {
		var id = '';
		var parts = src.split('/');
		id = parts[parts.length - 3] + '-' + parts[parts.length - 2] + '-' + parts[parts.length - 1];
		return id;
	},

	loadImage: function(src, i, callback, preload) {
		var imageId = this.getImageId(src);
		var image;

		if (this.images[imageId]) {
			image = this.images[imageId];
			if (preload) {
				return;
			}

			if (i === 0) {
				this.texture1 = image;
			} else if (i === 1) {
				this.texture2 = image;
			}
			this.loadImageTexture(image, i);
			this.empty = false;
			this.drawn = false;
			(callback) ? callback() : '';
			return;
		}

		image = new Image();

		this.requestCORSIfNotSameOrigin(image, src);

		image.src = src;

		var that = this;

		image.onload = function() {

			that.images[imageId] = image;
			if (preload) {
				return;
			}

			if (i === 0) {
				that.texture1 = image;
			} else {
				that.texture2 = image;
			}

			that.loadImageTexture(image, i);
			that.empty = false;
			that.drawn = false;
			(callback) ? callback() : '';
		};

	},

	requestCORSIfNotSameOrigin: function(image, src) {
		if ((new URL(src, window.location.href)).origin !== window.location.origin) {
			image.crossOrigin = '';
		}
	},

	loadImageTexture: function(image, i) {
		if (!this.gl) {
			return;
		}
		// Create texture
		var texture;

		if (this.loadedTextures[i]) {
			texture = this.loadedTextures[i];

			var textureID = this.gl.TEXTURE0 + i;

			this.gl.activeTexture(textureID);
			this.gl.bindTexture(this.gl.TEXTURE_2D, texture);

			// load image to texture
			this.gl.texImage2D(this.gl.TEXTURE_2D, 0, this.gl.RGBA, this.gl.RGBA, this.gl.UNSIGNED_BYTE, image);

			this.addUniform('1i', 'u_image' + i, i);
			this.addUniform('2f', 'u_image' + i + '_size', [
				image.width,
				image.height
			]);

		} else {
			texture = this.gl.createTexture();

			var textureID = this.gl.TEXTURE0 + i;

			this.gl.activeTexture(textureID);
			this.gl.bindTexture(this.gl.TEXTURE_2D, texture);

			// Set texture parameters to be able to draw any size image
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_WRAP_S, this.gl.CLAMP_TO_EDGE);
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_WRAP_T, this.gl.CLAMP_TO_EDGE);
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_MIN_FILTER, this.gl.LINEAR);
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_MAG_FILTER, this.gl.LINEAR);

			// load image to texture
			this.gl.texImage2D(this.gl.TEXTURE_2D, 0, this.gl.RGBA, this.gl.RGBA, this.gl.UNSIGNED_BYTE, image);

			this.addUniform('1i', 'u_image' + i, i);
			this.addUniform('2f', 'u_image' + i + '_size', [
				image.width,
				image.height
			]);

			// flip coordinates
			this.updateUniform('u_flipY', -1);
		}

	},

	replaceImage: function(src) {
		var that = this;
		var imageId = this.getImageId(src);

		if (this.texture2) {
			that.loadImageTexture(this.texture2, 0);
			that.loadImageTexture(this.texture2, 1);
		}

		var ease = function(t) { return t * (2 - t); };

		this.loadImage(src, 1, function() {
			var time = 1300;
			var fps = 60;
			var frameTime = 1000 / fps;
			var frames = time / frameTime;
			var step = 1 / frames;
			var requestID;
			var t = 0;

			function progress() {
				t += step;

				that.progress = ease(t);

				if (that.progress >= 1) {
					window.cancelAnimationFrame(requestID);
					return;
				}

				requestID = window.requestAnimationFrame(progress);
			}

			that.progress = 0;

			progress();
		});
	}
};
