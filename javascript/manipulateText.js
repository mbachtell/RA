  					function updateTextArea() {         
                 var allVals = [];
								 var languageCodes = [];
                 $('#c_b :checked').each(function() {
                   var toSplit = $(this).val();
									 stringSplit = toSplit.split(":");
									 allVals.push(stringSplit[1]);
									 languageCodes.push($(this).val());
                 });
								 jQuery.unique(allVals);
								 $('#t').val(allVals);
								 $('#languageCodes').val(languageCodes);
              }
             $(function() {
               $('#c_b input').click(updateTextArea);
               updateTextArea();
             });
