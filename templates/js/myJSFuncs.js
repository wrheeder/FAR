(function($) {
    $(window).unload(function() {
                alert('test');
            });
    $.each({
        test: function(msg) {

        },
        repeatString: function(str, num) {
            out = '';
            for (var i = 0; i < num; i++) {
                out += str;
            }
            return out;
        },
        dump: function(v, howDisplay, recursionLevel) {
            howDisplay = (typeof howDisplay === 'undefined') ? "alert" : howDisplay;
            recursionLevel = (typeof recursionLevel !== 'number') ? 0 : recursionLevel;


            var vType = typeof v;
            var out = vType;

            switch (vType) {
                case "number":
                    /* there is absolutely no way in JS to distinguish 2 from 2.0
                     so 'number' is the best that you can do. The following doesn't work:
                     var er = /^[0-9]+$/;
                     if (!isNaN(v) && v % 1 === 0 && er.test(3.0))
                     out = 'int';*/
                case "boolean":
                    out += ": " + v;
                    break;
                case "string":
                    out += "(" + v.length + '): "' + v + '"';
                    break;
                case "object":
                    //check if null
                    if (v === null) {
                        out = "null";

                    }
                    //If using jQuery: if ($.isArray(v))
                    //If using IE: if (isArray(v))
                    //this should work for all browsers according to the ECMAScript standard:
                    else if (Object.prototype.toString.call(v) === '[object Array]') {
                        out = 'array(' + v.length + '): {\n';
                        for (var i = 0; i < v.length; i++) {
                            out += this.repeatString('   ', recursionLevel) + "   [" + i + "]:  " +
                                    this.dump(v[i], "none", recursionLevel + 1) + "\n";
                        }
                        out += this.repeatString('   ', recursionLevel) + "}";
                    }
                    else { //if object    
                        sContents = "{\n";
                        cnt = 0;
                        for (var member in v) {
                            //No way to know the original data type of member, since JS
                            //always converts it to a string and no other way to parse objects.
                            sContents += this.repeatString('   ', recursionLevel) + "   " + member +
                                    ":  " + this.dump(v[member], "none", recursionLevel + 1) + "\n";
                            cnt++;
                        }
                        sContents += this.repeatString('   ', recursionLevel) + "}";
                        out += "(" + cnt + "): " + sContents;
                    }
                    break;
            }

            if (howDisplay == 'body') {
                var pre = document.createElement('pre');
                pre.innerHTML = out;
                document.body.appendChild(pre)
            }
            else if (howDisplay == 'alert') {
                alert(out);
            }else if (howDisplay == 'console') {
                console.log(out);
            }

            return out;
        }
    }, $.univ._import);
})($);