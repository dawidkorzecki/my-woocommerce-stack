woodmartThemeModule.shaders = {
	matrixVertex: '' +
		'attribute vec2 a_texCoord;' +
		'attribute vec2 a_position;' +
		'uniform mat3 u_matrix;' +
		'void main() {' +
		'	gl_Position = vec4( ( u_matrix * vec3(a_position, 1) ).xy, 0, 1);' +
		'	a_texCoord;' +
		'}',

	sliderWithNoise:
		'precision mediump float;' +
		'uniform float u_time;' +
		'uniform float u_progress;' +
		'uniform sampler2D u_image0;' +
		'uniform vec2 u_image0_size;' +
		'uniform sampler2D u_image1;' +
		'uniform vec2 u_image1_size;' +
		'uniform vec2 u_pixels;' +
		'uniform vec2 u_mouse;' +
		'uniform vec2 u_uvRate;' +
		'uniform float u_scale;' +
		'float rand(vec2 seed) {' +
		'	return fract(sin(dot(seed, vec2(1.29898,7.8233))) * 4.37585453123);' +
		'}' +
		'float noise(vec2 position) {' +
		'	vec2 block_position = floor(position);' +

		'	float top_left_value     = rand(block_position);' +
		'	float top_right_value    = rand(block_position + vec2(1.0, 0.0));' +
		'	float bottom_left_value  = rand(block_position + vec2(0.0, 1.0));' +
		'	float bottom_right_value = rand(block_position + vec2(1.0, 1.0));' +

		'	vec2 computed_value = smoothstep(0.0, 1.0, fract(position));' +

		'	return mix(top_left_value, top_right_value, computed_value.x)' +
		'		+ (bottom_left_value  - top_left_value)  * computed_value.y * (1.0 - computed_value.x)' +
		'		+ (bottom_right_value - top_right_value) * computed_value.x * computed_value.y' +
		'		- 0.5;' +
		'}' +
		'void main() {' +
		'	vec2 uv = gl_FragCoord.xy/u_pixels.xy;' +
		'	uv.y = 1.0 - uv.y;' +
		'	vec2 uv2 = uv;' +
		'	vec2 s = u_pixels.xy/10.;' +
		'	vec2 i = u_image0_size/10.;' +
		'	float rs = s.x / s.y;' +
		'	float ri = i.x / i.y;' +
		'	vec2 new = rs < ri ? vec2(i.x * s.y / i.y, s.y) : vec2(s.x, i.y * s.x / i.x);' +
		'	vec2 offset = (rs < ri ? vec2((new.x - s.x) / 2.0, 0.0) : vec2(0.0, (new.y - s.y) / 2.0)) / new;' +
		'	uv = uv * s / new + offset;' +

		'	i = u_image1_size/10.;' +
		'	ri = i.x / i.y;' +
		'	new = rs < ri ? vec2(i.x * s.y / i.y, s.y) : vec2(s.x, i.y * s.x / i.x);' +
		'	offset = (rs < ri ? vec2((new.x - s.x) / 2.0, 0.0) : vec2(0.0, (new.y - s.y) / 2.0)) / new;' +
		'	uv2 = uv2 * s / new + offset;' +

		'	float delayValue = clamp(u_progress, 0., 1.);' +
		'   float d = distance(u_mouse*u_uvRate, uv*u_uvRate);' +

		'	float ppp = ((u_progress - .5) * (u_progress - .5) - .25 );' +
		'	vec2 uv_offset = ppp * 1.1 * vec2( noise(uv * 10.0 + sin(u_time + uv.x * 5.0)) / 10.0, noise(uv * 10.0 + cos(u_time + uv.y * 5.0)) / 10.0);' +
		'	uv += uv_offset;' +
		'	uv2 += uv_offset;' +
		'	uv = (uv - vec2(.5, .5)) * u_scale + 0.5;' +
		'	vec4 rgba1 = texture2D( u_image0, uv );' +
		'	vec4 rgba2 = texture2D( u_image1, uv2 );' +
		'	vec4 rgba = mix(rgba1, rgba2, delayValue);' +
		'	gl_FragColor = rgba;' +
		// '	gl_FragColor = vec4(uv, 0., 1.);' +
		'}',

	sliderPattern:
		'precision mediump float;' +
		'uniform float u_time;' +
		'uniform float u_progress;' +
		'uniform sampler2D u_image0;' +
		'uniform vec2 u_image0_size;' +
		'uniform sampler2D u_image1;' +
		'uniform vec2 u_image1_size;' +
		'uniform sampler2D u_image2;' +
		'uniform vec2 u_image2_size;' +
		'uniform vec2 u_pixels;' +
		'void main() {' +
		'	vec2 uv = gl_FragCoord.xy/u_pixels.xy;' +
		'	uv.y = 1.0 - uv.y;' +
		'	vec2 uv2 = uv;' +
		'	vec2 s = u_pixels.xy/10.;' +
		'	vec2 i = u_image0_size/10.;' +
		'	float rs = s.x / s.y;' +
		'	float ri = i.x / i.y;' +
		'	vec2 new = rs < ri ? vec2(i.x * s.y / i.y, s.y) : vec2(s.x, i.y * s.x / i.x);' +
		'	vec2 offset = (rs < ri ? vec2((new.x - s.x) / 2.0, 0.0) : vec2(0.0, (new.y - s.y) / 2.0)) / new;' +
		'	uv = uv * s / new + offset;' +

		'	i = u_image1_size/10.;' +
		'	ri = i.x / i.y;' +
		'	new = rs < ri ? vec2(i.x * s.y / i.y, s.y) : vec2(s.x, i.y * s.x / i.x);' +
		'	offset = (rs < ri ? vec2((new.x - s.x) / 2.0, 0.0) : vec2(0.0, (new.y - s.y) / 2.0)) / new;' +
		'	uv2 = uv2 * s / new + offset;' +

		'vec4 disp = texture2D(u_image2, uv);' +
		'float effectFactor = 0.4;' +

		'vec2 distortedPosition = vec2(uv.x + u_progress * (disp.r*effectFactor), uv.y);' +
		'vec2 distortedPosition2 = vec2(uv.x - (1.0 - u_progress) * (disp.r*effectFactor), uv.y);' +

		'vec4 _texture = texture2D(u_image0, distortedPosition);' +
		'vec4 _texture2 = texture2D(u_image1, distortedPosition2);' +

		'vec4 finalTexture = mix(_texture, _texture2, u_progress);' +
		'gl_FragColor = finalTexture;' +
		// '	gl_FragColor = vec4(uv, 0., 1.);' +
		'}',

	sliderWithWave:
		'precision mediump float;' +
		'uniform float u_time;' +
		'uniform float u_progress;' +
		'uniform sampler2D u_image0;' +
		'uniform vec2 u_image0_size;' +
		'uniform sampler2D u_image1;' +
		'uniform vec2 u_image1_size;' +
		'uniform vec2 u_pixels;' +
		'uniform vec2 u_mouse;' +
		'uniform vec2 u_uvRate;' +
		'uniform float u_scale;' +

		'    vec2 mirrored(vec2 v) {' +
		'        vec2 m = mod(v,2.);' +
		'        return mix(m,2.0 - m, step(1.0 ,m));' +
		'    }' +

		'    float tri(float p) {' +
		'        return mix(p,1.0 - p, step(0.5 ,p))*2.;' +
		'    }' +

		'void main() {' +
		'	vec2 uv = gl_FragCoord.xy/u_pixels.xy;' +
		'	uv.y = 1.0 - uv.y;' +
		'	vec2 uv2 = uv;' +
		'	vec2 s = u_pixels.xy/10.;' + // problem on mobile devices that is why we scale the value by 10x
		'	vec2 i = u_image0_size.xy/10.;' + // problem on mobile devices that is why we scale the value by 10x
		'	float rs = s.x / s.y;' + // 0.646
		'	float ri = i.x / i.y;' + // 2.23
		'	vec2 new = rs < ri ? vec2(i.x * s.y / i.y, s.y) : vec2(s.x, (i.y * s.x) / i.x);' + // 375. 167.9
		'	vec2 offset = (rs < ri ? vec2((new.x - s.x) / 2.0, 0.0) : vec2(0.0, (new.y - s.y) / 2.0)) / new;' +
		'	uv = uv * s / new + offset;' +
		'	i = u_image1_size.xy/10.;' +
		'	ri = i.x / i.y;' +
		'	new = rs < ri ? vec2(i.x * s.y / i.y, s.y) : vec2(s.x, i.y * s.x / i.x);' +
		'	offset = (rs < ri ? vec2((new.x - s.x) / 2.0, 0.0) : vec2(0.0, (new.y - s.y) / 2.0)) / new;' +
		'	uv2 = uv2 * s / new + offset;' +

		'    float delayValue = u_progress*6.5 - uv.y*2. + uv.x - 3.0;' +
		'    vec2 accel = vec2(0.5,2.);' +

		'    delayValue = clamp(delayValue,0.,1.);' +

		'    vec2 translateValue = u_progress + delayValue*accel;' +
		'    vec2 translateValue1 = vec2(-0.5,1.)* translateValue;' +
		'    vec2 translateValue2 = vec2(-0.5,1.)* (translateValue - 1. - accel);' +

		'    vec2 w = sin( sin(u_time) * vec2(0,0.3) + uv.yx*vec2(0,4.))*vec2(0,0.5);' +
		'    vec2 xy = w*(tri(u_progress)*0.5 + tri(delayValue)*0.5);' +

		'    vec2 uv1 = uv + translateValue1 + xy;' +
		'    uv2 = uv2 + translateValue2 + xy;' +

		'    vec4 rgba1 = texture2D(u_image0,mirrored(uv1));' +
		'    vec4 rgba2 = texture2D(u_image1,mirrored(uv2));' +

		'    vec4 rgba = mix(rgba1,rgba2,delayValue);' +
		// '	gl_FragColor = vec4(0.1,0.1,0.1, 1.);' +
		'	gl_FragColor = rgba;' +
		'}',

	hoverWave:
		'precision mediump float;' +
		'uniform float u_time;' +
		'uniform float u_progress;' +
		'uniform sampler2D u_image;' +
		'uniform vec2 u_pixels;' +
		'uniform vec2 u_mouse;' +
		'uniform vec2 u_uvRate;' +
		'uniform float u_scale;' +

		'void main() {' +
		'	vec2 uv = gl_FragCoord.xy/u_pixels.xy;' +
		'	uv.y = 1.0 - uv.y;' +
		'	float d = distance(u_mouse*u_uvRate, uv*u_uvRate);' +
		'	float ppp = ((u_progress - .5) * (u_progress - .5) - .25 );' +
		'	float dY = sin(uv.y * 44.005 + u_time * 4.5) * 0.02 * ppp;' +
		'	float dX = sin(uv.x * 30.005 + u_time * 3.2) * 0.02 * ppp;' +
		'	if( u_progress > 0. && d < .1 ) {' +
		'	   dX *= smoothstep( 0., .15, (.15 - d) ) * 5.;' +
		'	   dY *= smoothstep( 0., .15, (.15 - d) ) * 5.;' +
		'	}' +
		'	uv.y += dY;' +
		'	uv.x += dX;' +
		'	gl_FragColor = texture2D(u_image, uv);' +
		'}'
};
