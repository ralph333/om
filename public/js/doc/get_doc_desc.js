$(document).ready(function()
{ 
	$.post("/doc/getdesc",
	function(data){
		var desc = [];
		for(var i=0;i<data.length;i++) {
			desc.push(data[i]);
		};
	    var substringMatcher = function (strs) {
            return function findMatches(q, cb) {
                var matches, substrRegex;
                matches = [];
                substrRegex = new RegExp(q, 'i');
                $.each(strs, function (i, str) {
                    if (substrRegex.test(str)) {
                        matches.push({ value: str });
                    }
                });
                cb(matches);
            };
        };

        $('#doc_monify_desc').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'desc',
            displayKey: 'value',
            source: substringMatcher(desc)
        });
        alert('value');
	});
});