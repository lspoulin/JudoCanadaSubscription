        
        function ArrayInput (wrapperId, max_fields){
            var o = this;
            this.max_fields      =  max_fields || 10; //maximum input boxes allowed
            this.wrapperId       =  wrapperId;
            this.wrapperSelector       = "#" + wrapperId;
            this.buttonAddSelector = this.wrapperSelector + " .add_field_button";
            this.duplicatableSelector = this.wrapperSelector + " .duplicatable";
            this.x = 1;
            this.init = function(){
                $(this.buttonAddSelector).click(function(e){ //on add input button click
                    e.preventDefault();
                    if(o.x < o.max_fields){ //max input box allowed
                        o.x++; //text box increment
                        var html = '<div>' + $(o.duplicatableSelector).html() + '<a href="#" class="remove_field">Enlever</a></div>';
                        $(o.wrapperSelector).append(html);
                    }
                });

                $(this.wrapperSelector).on("click",".remove_field", function(e){ //user click on remove text
                    e.preventDefault();
                    $(this).parent('div').remove();
                    o.x--;
                });
            };
        };


        function ArrayInputToTable (wrapperId, max_fields){
            var o = this;
            this.max_fields      =  max_fields || 10; //maximum input boxes allowed
            this.wrapperId       =  wrapperId;
            this.wrapperSelector       = "#" + wrapperId;
            this.buttonAddSelector = this.wrapperSelector + " button";
            this.tableSelector = this.wrapperSelector + " .appendable tbody";
            this.elements =
            this.x = 1;
            this.init = function(){
                $(o.wrapperSelector).append("<button class=\"w3-button w3-circle w3-red\">+</button>");
                    var $elements = $(o.wrapperSelector).find("input").not("input[type='button']");
                    var html="";
                      for(var i=0; i<$elements.length;i++) {
                            html+='<th>'+ $elements[i].placeholder +'</th>';
                        }
                 $(o.wrapperSelector).append("<table class=\"appendable\"><tr>"+html+"<th></th></tr></table>");
                $(o.tableSelector).hide();
                $(this.buttonAddSelector).click(function(e){ //on add input button click
                    e.preventDefault();
                    if(o.x < o.max_fields){ //max input box allowed
                        o.x++; //text box increment

                        var $elements = $(o.wrapperSelector).find("input").not("input[type='button']");

                        var html = '<tr>';
                        for(var i=0; i<$elements.length;i++) {
                            html+='<td>'+ $elements[i].value +'</td>';
                        }
                        html+='<td><button class=\"remove_field w3-button w3-circle w3-red\">x</button></td></tr>';

                        $(o.tableSelector).append(html);
                        $(o.tableSelector).show();
                    }
                });

                $(this.wrapperSelector).on("click",".remove_field", function(e){ //user click on remove text
                    e.preventDefault();
                    $(this).closest('tr').remove();
                    o.x--;
                    if(o.x == 1){
                       $(o.tableSelector).hide();
                    }

                });
            };
        };
