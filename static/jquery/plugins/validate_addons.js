jQuery.validator.addMethod("regexp", function(value, element, param) {
	if (this.optional(element)) {
		return true;
	}
	if (typeof param === 'string') {
		param = new RegExp('^(?:' + param + ')$');
	}
	return param.test(value);
}, "Invalid format.");
jQuery.validator.addMethod("ip", function(value, element, param) {
	return this.optional(element) || /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(value);
}, "Please enter a valid IP v4 address.");
jQuery.validator.addMethod("accept", function(value, element, param) {
	// Split mime on commas incase we have multiple types we can accept
	var typeParam = typeof param === "string" ? param.replace(/,/g, '|') : "image/*",
	optionalValue = this.optional(element),
	i, file;

	// Element is optional
	if(optionalValue) {
		return optionalValue;
	}

	if($(element).attr("type") === "file") {
		// If we are using a wildcard, make it regex friendly
		typeParam = typeParam.replace("*", ".*");

		// Check if the element has a FileList before checking each file
		if(element.files && element.files.length) {
			for(i = 0; i < element.files.length; i++) {
				file = element.files[i];

				// Grab the mimtype from the loaded file, verify it matches
				if(!file.type.match(new RegExp( ".?(" + typeParam + ")$", "i"))) {
					return false;
				}
			}
		}
	}

	// Either return true because we've validated each file, or because the
	// browser does not support element.files and the FileList feature
	return true;
}, jQuery.format("Please enter a value with a valid mimetype."));

$.validator.addMethod('url3', function(value, element) {
    return this.optional(element) || /^(https?:\/\/|\/).*$/.test(value);
}, 'Invalid format.');

$.validator.addMethod('callback', function(value, element,callback) {
    return this.optional(element) || callback(value,element);
}, 'Invalid format.');