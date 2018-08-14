function addStepSpan(){
            $("#idDivPageIndicator").html("");
            var html = "<span class=\"step\"></span>";
            var $newdiv1 = $(html);
            for(var i = 0; i < pages.length; i++){
               if(i == currentPage){
                   $newdiv1.addClass("active");
               }
               else if (i < currentPage){
                    $newdiv1.addClass("finish");
               }
               $("#idDivPageIndicator").append($newdiv1);
               $newdiv1 = $(html);
            }
        }

        function changePageUp(){
            if(currentPage >= pages.length - 1 ){
                alert("congratulations, you reached the end of the form");
            }
            else{
              collectData(pages[currentPage]);
              if(validate(pages[currentPage])){
                changePage(pages[currentPage], pages[++currentPage]);
              }
              else{
                alert(data[pages[currentPage]].message);
              }
            }
                
         }


        function changePageDown(){
             if(currentPage <= 0){
                alert("Wow something went wrong body");
            }
            else
                changePage(pages[currentPage], pages[--currentPage]);
         }

        function changePage(idPage, idNextPage){
                animate(idPage, idNextPage);
                scrollToDiv("primary");
                addStepSpan();
        }

        function setPage(pageId) {
             currentPage = pageId;

             for(var i=0; i<= pages.length ; i++){

                if(i == currentPage)
                    $('#'+ pages[i]).show();
                else
                    $('#'+ pages[i]).hide();
             }
             addStepSpan();
        }

        function animate(idPage, idNextPage) {
             $('#'+idPage).fadeOut(1000, function(){
                    $('#'+idNextPage).fadeIn();
                    });
        }
        function scrollToDiv(div){
          $('html, body').animate({
                    scrollTop: $("#"+div).offset().top
                }, 'slow');
        }

        function scrollToPosition(n){
           $('html, body').animate({scrollTop:n}, 'slow');
        }