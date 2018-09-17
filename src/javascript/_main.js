var data = {};
    var points = {};
    var points = {};
    var index = 0;
    var currentPage = 0;

    var instructorsInput = new ArrayInput("input_instructor_wrapper");
    var pointInput = new ArrayInput("input_point_system_wrapper");
    var pointInput2 = new ArrayInput("input_point_system_wrapper2");
    var sportResult = new ArrayInput("input_sport_result_wrapper", 5);
    var trainer = new ArrayInput("input_trainer_wrapper", 4);
    var instructorTraining = new ArrayInput("input_instructor_training_wrapper", 4);
    var katalist = new ArrayInput("input_kata_list_wrapper", 4);
    var contributionList = new ArrayInput("input_contribution_wrapper", 4);

    $(document).ready( function(){
        setPage(currentPage);
        $(".button-next-page").click(function(){calculatePoints();createTableSummaryPoint();changePageUp();});
        $(".button-previous-page").click(function(){changePageDown();});
        instructorsInput.init();
        pointInput.init();
        pointInput2.init();
        sportResult.init();
        trainer.init();

        createInputPromotionDan();
        createInputYearActive();
        createTableSummaryPoint();
        initData();
        $("input.date").dcalendarpicker();
        $("#rule_technical_points").html(rules_technical_points);
        $("#rules_tournament").html(rules_tournament);
        $("#idSelectCountry").html(COUNTRY_SELECT_HTML);

    } );

    function initData(){
      for (index = 0; index < pages.length ; index++){
        data[pages[index]] = {};
        $("#"+pages[index]+" :input").each(function( ) {
          let member = $(this).attr('name');
            data[pages[index]][member] = "";
        });
      }
    }

    function collectData(page){
      $("#"+page+" :input").each(function( ) {
          let member = $(this).attr('name');
          let value = $(this).val();
          if(value != null && value.length!=0){
            data[page][member] = value;
          }
        });
    }

    function validate(page){
      return true;
      let message = "";
       $("#"+page+" :input").each(function( ) {
          if($(this).attr('required') == "required"){
            let member = $(this).attr('placeholder') || $(this).attr('name');
            let value = $(this).val();
            if(value == null || value.length==0){
              message += member + " cannot be empty.<br>";
            }
          }
        });
       data[page]["message"] = message;
       return message.length == 0;
    }

    function createInputPromotionDan(){
      let html = "";
        for (let i = 1; i <= 9; i++)
          for (let j=0; j < 3 ; j++)
            html += "<input type='text' class='w3-input date' placeholder='"+i + ((i==1)?"er ":"ieme ") + labelsPromotionDan[j]+"'/>";

        $("#idDivPromotionDanInput").html(html);
        $("input.date").dcalendarpicker();
    }

    function createInputYearActive(){
      let html = "<table id='idTablePoints'><tr><th>&nbsp;</th>";

        let year = getCurrentYear();
        for (let i = year ; i >= yearMin; i--){
          html+="<th>" + i +"</th>";
        }
        for (let i = 0 ; i <= year - yearMin + 1; i++){
            if (i==0){
              html+="<tr><td>Niveau</td>";
            }
            else{
              let index = "select"+(getCurrentYear()-(i-1));
              html+="<td><select id=\""+index+"\" name=\""+index+"\" class=\"w3-input\"><option value=\"\" selected=\"selected\">";

              for(let key in pointYearActive) {

                html+="<option value=\""+key+"\">"+key+"</option>";
              }
              html+="</select></td>";
            }
          }
         html+="</tr></table>";
        $("#divYearActive").html(html);
    }

    function createTableSummaryPoint(){
        let html = "<table id='idTablePoints'><tr><th>&nbsp;</th>";
        let year = getCurrentYear();
        for (let i = year ; i >= yearMin; i--){
          html+="<th>" + i +"</th>";
        }

        for(let j= 0 ; j < labels.length ; j++){
          html+="</tr><tr>";
          let label =  labels[j].label;
          let type = labels[j].type;

          for (let i = 0 ; i <= year - yearMin + 1; i++){
            if (i==0){
              html+="<td>" + label +"</td>";
            }
            else{
              let index = getCurrentYear()-(i-1);
              let point = 0;
              if(typeof points[type] !== 'undefined' && typeof points[type][index] !== 'undefined'){
                 point =  parseInt(points[type][index]);
              }
              html+="<td>"+point +"</td>";
            }
          }
        }
        html +="</tr></table>";

        $('#idPointTableSummary').html(html);
        $('#idTablePoints tr:odd').addClass("w3-grey");
    }

    function calculatePoints(){
        calculateYearsInJudo();
        calculateParticipationKata();
        calculatePointTechniques();
        calculatePointNonTechniques();
        calculGrandTotal();
    }

    function  calculateYearsInJudo(){
        points["year_active"] = [];
        let year = getCurrentYear();
        for (let i = year;i >= yearMin; i--) {
           let index = "select"+i;
            let key = $("#"+index).val() || "";
            points["year_active"][i] = pointYearActive[key];
        }
    }

    function calculateParticipationKata(){
        points["participation_kata"] = [];
        points["participation_shiai"] = [];
        points["tournois_shiai"] = [];
        points["tournois_kata"] = [];
        let contestdates = $( "input[name='grade_date[]']" );
        let pointscontest = $( "input[name='points[]']" );
        let gradetypes = $( "select[name='grade_type[]']" );

        contestdates.each(function(index, value){
            let val = value.value;
            if(val.length>0){
                let d = new Date(val);
                let n = d.getFullYear();
                let suffix = (gradetypes.get(index).value || "shiai");
                let pts = parseInt(pointscontest.get(index).value);

                let index_point = "participation_"+ suffix;
                let index_point_contest = "tournois_"+ suffix;

                if(suffix.length>0) {
                    if (index == 0 ){
                        points["participation_kata"][n] =points["participation_kata"][n] || 0;
                        points["participation_shiai"][n] = points["participation_shiai"][n] || 0;
                        points["tournois_kata"][n] = points["tournois_kata"][n] || 0;
                        points["tournois_shiai"][n] = points["tournois_shiai"][n] || 0;
                    }
                     let participation = points[index_point][n];
                     points[index_point_contest][n]+= pts;

                     if(participation < 60){
                         participation = Math.min(60, participation + 5);
                     }
                     points[index_point][n] = participation;
                 }
            }
         });
    }

    function calculatePointTechniques(){
        for (let i in pointTechniques) {
            points[i] = [];
        }
        let contestdates = $( "input[name='grade_date2[]']" );
        let gradeCodes = $( "select[name='grade_code2[]']" );

        contestdates.each(function(index, value){
             let val = value.value;
             if(val.length>0){
                let d = new Date(val);
                let n = d.getFullYear();

                 let code =  $( "select[name='grade_code2[]'] option:selected" ).attr("value");
                 let categorie =  $( "select[name='grade_code2[]'] option:selected" ).attr("category");

                 if(code.length>0 && categorie.length>0){
                     if (index == 0 ){
                         for (let i in pointTechniques) {
                            points[i][n] = points[i][n] || 0;
                         }
                         points[categorie][n] += pointTechniques[categorie][code];
                         points[categorie][n] = Math.min(points[categorie][n], pointTechniques[categorie]['MAX']) ;
                    }
                 }
             }
        });
    }

    function calculatePointNonTechniques(){

    }

    function calculGrandTotal(){
        let total = 0;
        let tc_total = 0;

        for(let i in points){

            for (let j in points[i]) {
                if (i!= 'N2' || i!='year_active'){
                    tc_total += parseInt(points[i][j]);
                }
                 total += parseInt(points[i][j]);
            }
        }
        $("#total_tc_points").text(tc_total);
        $("#total_points").text(total);
    }
