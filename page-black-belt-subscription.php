<?php
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    //echo "request is a post ";
    $type = $_POST['type']; 
    switch ($type) { 
        case 'email': 
            sendEmail(); 
            break; 
        case 'validate':
            validate();
            break; 
    } 
    exit(); 
} 
 
function sendEmail(){ 
    echo "send email please setup the smtp server";
    $msg = "First line of text\nSecond line of text";
    // use wordwrap() if lines are longer than 70 characters 
    $msg = wordwrap($msg,70); 
    // send email 
    /*ini_set('SMTP','myserver'); 
    ini_set('smtp_port',25); 
    mail("lspoulin@gmail.com","My subject",$msg);
     */ 
} 

function validate(){
   $data = json_decode($_POST['data'], true);
   var_dump($data);
}
?>

<?php get_header(); ?>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/w3.css';?>">
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/dcalendar.picker.css';?>">
  <script src="<?php echo get_stylesheet_directory_uri().'/jquery-3.3.1.min.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/object.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/navigation.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/judo_info.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/util.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/dcalendar.picker.js';?>"></script>

    <script>


        var data = {};
        var points = {};
        var index = 0;
        var currentPage =3;

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
            alert("test");
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
        } );

        function sendMail(){
            $.ajax({
              type: "POST",
              url: "<?php echo get_stylesheet_directory_uri().'/page-black-belt-subscription.php';?>",
              data: {type: "email"},
              success: function(response) {
                  alert( "success " + response);
                }
            });
        }

        function sendForm(){
          var url = 'http://example.com/vote/' + Username;
          var form = $('<form action="' + url + '" method="post">' +
            '<input type="text" name="api_url" value="' + Return_URL + '" />' +
            '</form>');
          $('body').append(form);
          form.submit();
        }


        function initData(){
          for (index = 0; index < pages.length ; index++){
            data[pages[index]] = {};
            $("#"+pages[index]+" :input").each(function( ) {
              var member = $(this).attr('name');
                data[pages[index]][member] = "";
            });              
          }
        }

        function collectData(page){
          $("#"+page+" :input").each(function( ) {
              var member = $(this).attr('name');
              var value = $(this).val();
              if(value != null && value.length!=0){
                data[page][member] = value;
              }
            });
        }

        function validate(page){

          return true;
          var message = "";
           $("#"+page+" :input").each(function( ) {
              if($(this).attr('required') == "required"){
                var member = $(this).attr('placeholder') || $(this).attr('name');
                var value = $(this).val();
                if(value == null || value.length==0){
                  message += member + " cannot be empty.<br>";
                }
              }
            });
           data[page]["message"] = message;
           return message.length == 0;
        }

        function createInputPromotionDan(){
          var html = "";
            for (var i = 1; i <= 9; i++)
              for (var j=0; j < 3 ; j++)
                html += "<input type='text' class='w3-input date' placeholder='"+i + ((i==1)?"er ":"ieme ") + labelsPromotionDan[j]+"'/>";

            $("#idDivPromotionDanInput").html(html);
            $("input.date").dcalendarpicker();
        }

        function createInputYearActive(){

          var html = "<table id='idTablePoints'><tr><th>&nbsp;</th>";
          
            var year = getCurrentYear();
            for (var i = year ; i >= yearMin; i--){
              html+="<th>" + i +"</th>";
            }
            for (var i = 0 ; i <= year - yearMin + 1; i++){
                if (i==0){
                  html+="<tr><td>Niveau</td>";
                }
                else{
                  var index = "select"+(getCurrentYear()-(i-1));
                  html+="<td><select id=\""+index+"\" name=\""+index+"\" class=\"w3-input\"><option value=\"\" selected=\"selected\">";

                  for(var key in pointYearActive) {
                    
                    html+="<option value=\""+key+"\">"+key+"</option>";
                  } 
                  html+="</select></td>";
                }
              }
             html+="</tr></table>"; 
            $("#divYearActive").html(html);
        }

        function createTableSummaryPoint(){
            var html = "<table id='idTablePoints'><tr><th>&nbsp;</th>";
            var year = getCurrentYear();
            for (var i = year ; i >= yearMin; i--){
              html+="<th>" + i +"</th>";
            }

            for(var j= 0 ; j < labels.length ; j++){
              html+="</tr><tr>";
              let label =  labels[j].label;
              let type = labels[j].type;

              for (var i = 0 ; i <= year - yearMin + 1; i++){
                if (i==0){
                  html+="<td>" + label +"</td>";
                  break;
                }
                let index = getCurrentYear()-(i-1);
                let point = 0;
                if(typeof points[type] !== 'undefined' && typeof points[type][index] !== 'undefined'){
                   point =  parseInt(points[type][index]);
                   alert(point);
                }
                html+="<td>"+point +"</td>";

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

        function calculGrandTotal(){
            var total = 0;
            var tc_total = 0;

            for(var i in points){

                for (var j in points[i]) {
                    if (i!= 'N2' || i!='year_active'){
                        tc_total += parseInt(points[i][j]);
                    }
                     total += parseInt(points[i][j]);
                }
            }
            $("#total_tc_points").text(tc_total);
            $("#total_points").text(total);
        }

        function  calculateYearsInJudo(){
            points["year_active"] = [];
            var year = getCurrentYear();
            for (var i = year;i >= yearMin; i--) {
               var index = "select"+i;
                var key = $("#"+index).val() || "";
                points["year_active"][i] = pointYearActive[key];
            }

        }

        function dump(obj) {
            var out = '';
            for (var i in obj) {
                out += i + ": " + obj[i] + "\n";
            }

            alert(out);
        }

        function calculateParticipationKata(){
             points["participation_kata"] = [];
             points["participation_shiai"] = [];
             points["tournois_shiai"] = [];
             points["tournois_kata"] = [];
             var contestdates = $( "input[name='grade_date[]']" );
             var pointscontest = $( "input[name='points[]']" );
             var gradetypes = $( "select[name='grade_type[]']" );

             contestdates.each(function(index, value){

                   var val = value.value;
                     if(val.length>0){
                        var d = new Date(val);
                        var n = d.getFullYear();
                        var suffix = (gradetypes.get(index).value || "shiai");
                        var pts = parseInt(pointscontest.get(index).value);

                        var index_point = "participation_"+suffix;
                        var index_point_contest = "tournois_"+suffix;

                            if(suffix.length>0) {
                                if (index == 0 ){
                                    points["participation_kata"][n] =points["participation_kata"][n] || 0;
                                    points["participation_shiai"][n] = points["participation_shiai"][n] || 0;
                                    points["tournois_kata"][n] = points["tournois_kata"][n] || 0;
                                    points["tournois_shiai"][n] = points["tournois_shiai"][n] || 0;
                                }
                                 var participation = points[index_point][n];
                                 points[index_point_contest][n]+= pts;

                                 if(participation < 60){
                                     var participation = Math.min(60, participation + 5);
                                 }
                                 points[index_point][n] = participation;


                             }

                }
             });
        }

        function calculatePointNonTechniques(){

        }

        function calculatePointTechniques(){
            for (var i in pointTechniques) {
                points[i] = [];
            }
            var contestdates = $( "input[name='grade_date2[]']" );
            var gradeCodes = $( "select[name='grade_code2[]']" );

            contestdates.each(function(index, value){
                 var val = value.value;
                 if(val.length>0){
                    var d = new Date(val);
                    var n = d.getFullYear();

                     var code =  $( "select[name='grade_code2[]'] option:selected" ).attr("value");
                     var categorie =  $( "select[name='grade_code2[]'] option:selected" ).attr("category");

                     if(code.length>0 && categorie.length>0){
                         if (index == 0 ){
                             for (var i in pointTechniques) {
                                points[i][n] = points[i][n] || 0;
                             }
                             points[categorie][n] += pointTechniques[categorie][code];
                             points[categorie][n] = Math.min(points[categorie][n], pointTechniques[categorie]['MAX']) ;

                        }
                     }
                 }
            });
        }

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
              //collectData(pages[currentPage]);
              //if(validate(pages[currentPage])){
                changePage(pages[currentPage], pages[++currentPage]);
              //}
              //else{
              //  alert(data[pages[currentPage]].message);
              //}
            }
                
         }

    </script>
    <style>
        /* Make circles that indicate the steps of the form: */
        .step {
          height: 15px;
          width: 15px;
          margin: 0 2px;
          background-color: #bbbbbb;
          border: none;
          border-radius: 50%;
          display: inline-block;
          opacity: 0.5;
        }

        /* Mark the active step: */
        .step.active {
          opacity: 1;
        }

        /* Mark the steps that are finished and valid: */
        .step.finish {
          background-color: #4CAF50;
        }

    </style>
    <div id="primary" class="site-content">
        <div id="content" role="main">
           <div class="w3-container">
              <h1><?php the_title(); ?></h1>
            </div>

          <div id="idDivPageIndicator" style="text-align:center;margin-top:40px;margin-bottom:40px;margin-left:40px;margin-right:40px;">

                            </div>

                            <!-- premiere page du formulaire -->
                            <div id="idDivFormPersonalInformations" class="w3-container" style="display:none">
                                <div class="w3-card-4 ">

                                <div class="w3-container w3-green">
                                  <h2>Informations Personnelles</h2>
                                </div>
                                <div class="w3-container" style="margin-top:40px;margin-bottom:40px;margin-left:40px;margin-right:40px;">
                                  <p><label for="name">Votre nom : <span class="w3-text-red">*</span>
                                      <br><table><tr><td><input class="w3-input" id="firstname" name="firstname" placeholder="Pr&eacute;nom" type="text"  value="" required="required"> </td>
                                      <td><input class="w3-input" id="lastname" name="lastname" placeholder="Nom" type="text" value="" required="required">  </td></tr></table>
                                  </label></p>

                                  <p><label for="adress">Votre adresse : <span class="w3-text-red">*</span>
                                    <table><tr><td colspan="2">
                                      <br><input class="w3-input" id="adress1" name="adress1" placeholder="Adresse" type="text"  value=""></td></tr>
                                      <tr><td colspan="2">
                                      <br><input class="w3-input" id="adress2" name="adress2" placeholder="Adresse line 2" type="text"  value=""></td></tr>
                                      <tr><td>
                                      <br><input class="w3-input" id="city" name="city" placeholder="Ville" type="text" value=""> </td><td>
                                      <br><input class="w3-input" id="state" name="state" placeholder="Etat" type="text"  value=""> </td></tr><tr><td>
                                      <br><input class="w3-input" id="zipcode" name="zipcode" placeholder="Code postal" type="text"  value=""> </td><td>

                                      <br><label for="adress">Pays:<br><select class="w3-input" type="text" id="country" name="country" ><option value=""></option><option value="Afghanistan" >Afghanistan</option><option value="Albania" >Albania</option><option value="Algeria" >Algeria</option><option value="Andorra" >Andorra</option><option value="Angola" >Angola</option><option value="Antigua and Barbuda" >Antigua and Barbuda</option><option value="Argentina" >Argentina</option><option value="Armenia" >Armenia</option><option value="Australia" >Australia</option><option value="Austria" >Austria</option><option value="Azerbaijan" >Azerbaijan</option><option value="Bahamas" >Bahamas</option><option value="Bahrain" >Bahrain</option><option value="Bangladesh" >Bangladesh</option><option value="Barbados" >Barbados</option><option value="Belarus" >Belarus</option><option value="Belgium" >Belgium</option><option value="Belize" >Belize</option><option value="Benin" >Benin</option><option value="Bhutan" >Bhutan</option><option value="Bolivia" >Bolivia</option><option value="Bosnia and Herzegovina" >Bosnia and Herzegovina</option><option value="Botswana" >Botswana</option><option value="Brazil" >Brazil</option><option value="Brunei" >Brunei</option><option value="Bulgaria" >Bulgaria</option><option value="Burkina Faso" >Burkina Faso</option><option value="Burundi" >Burundi</option><option value="Cambodia" >Cambodia</option><option value="Cameroon" >Cameroon</option><option value="Canada" selected="selected">Canada</option><option value="Cape Verde" >Cape Verde</option><option value="Central African Republic" >Central African Republic</option><option value="Chad" >Chad</option><option value="Chile" >Chile</option><option value="China" >China</option><option value="Colombia" >Colombia</option><option value="Comoros" >Comoros</option><option value="Congo (Brazzaville)" >Congo (Brazzaville)</option><option value="Congo" >Congo</option><option value="Costa Rica" >Costa Rica</option><option value="Cote d'Ivoire" >Cote d'Ivoire</option><option value="Croatia" >Croatia</option><option value="Cuba" >Cuba</option><option value="Cyprus" >Cyprus</option><option value="Czech Republic" >Czech Republic</option><option value="Denmark" >Denmark</option><option value="Djibouti" >Djibouti</option><option value="Dominica" >Dominica</option><option value="Dominican Republic" >Dominican Republic</option><option value="East Timor (Timor Timur)" >East Timor (Timor Timur)</option><option value="Ecuador" >Ecuador</option><option value="Egypt" >Egypt</option><option value="El Salvador" >El Salvador</option><option value="Equatorial Guinea" >Equatorial Guinea</option><option value="Eritrea" >Eritrea</option><option value="Estonia" >Estonia</option><option value="Ethiopia" >Ethiopia</option><option value="Fiji" >Fiji</option><option value="Finland" >Finland</option><option value="France" >France</option><option value="Gabon" >Gabon</option><option value="Gambia, The" >Gambia, The</option><option value="Georgia" >Georgia</option><option value="Germany" >Germany</option><option value="Ghana" >Ghana</option><option value="Greece" >Greece</option><option value="Grenada" >Grenada</option><option value="Guatemala" >Guatemala</option><option value="Guinea" >Guinea</option><option value="Guinea-Bissau" >Guinea-Bissau</option><option value="Guyana" >Guyana</option><option value="Haiti" >Haiti</option><option value="Honduras" >Honduras</option><option value="Hungary" >Hungary</option><option value="Iceland" >Iceland</option><option value="India" >India</option><option value="Indonesia" >Indonesia</option><option value="Iran" >Iran</option><option value="Iraq" >Iraq</option><option value="Ireland" >Ireland</option><option value="Israel" >Israel</option><option value="Italy" >Italy</option><option value="Jamaica" >Jamaica</option><option value="Japan" >Japan</option><option value="Jordan" >Jordan</option><option value="Kazakhstan" >Kazakhstan</option><option value="Kenya" >Kenya</option><option value="Kiribati" >Kiribati</option><option value="Korea, North" >Korea, North</option><option value="Korea, South" >Korea, South</option><option value="Kuwait" >Kuwait</option><option value="Kyrgyzstan" >Kyrgyzstan</option><option value="Laos" >Laos</option><option value="Latvia" >Latvia</option><option value="Lebanon" >Lebanon</option><option value="Lesotho" >Lesotho</option><option value="Liberia" >Liberia</option><option value="Libya" >Libya</option><option value="Liechtenstein" >Liechtenstein</option><option value="Lithuania" >Lithuania</option><option value="Luxembourg" >Luxembourg</option><option value="Macedonia" >Macedonia</option><option value="Madagascar" >Madagascar</option><option value="Malawi" >Malawi</option><option value="Malaysia" >Malaysia</option><option value="Maldives" >Maldives</option><option value="Mali" >Mali</option><option value="Malta" >Malta</option><option value="Marshall Islands" >Marshall Islands</option><option value="Mauritania" >Mauritania</option><option value="Mauritius" >Mauritius</option><option value="Mexico" >Mexico</option><option value="Micronesia" >Micronesia</option><option value="Moldova" >Moldova</option><option value="Monaco" >Monaco</option><option value="Mongolia" >Mongolia</option><option value="Morocco" >Morocco</option><option value="Mozambique" >Mozambique</option><option value="Myanmar" >Myanmar</option><option value="Namibia" >Namibia</option><option value="Nauru" >Nauru</option><option value="Nepal" >Nepal</option><option value="Netherlands" >Netherlands</option><option value="New Zealand" >New Zealand</option><option value="Nicaragua" >Nicaragua</option><option value="Niger" >Niger</option><option value="Nigeria" >Nigeria</option><option value="Norway" >Norway</option><option value="Oman" >Oman</option><option value="Pakistan" >Pakistan</option><option value="Palau" >Palau</option><option value="Panama" >Panama</option><option value="Papua New Guinea" >Papua New Guinea</option><option value="Paraguay" >Paraguay</option><option value="Peru" >Peru</option><option value="Philippines" >Philippines</option><option value="Poland" >Poland</option><option value="Portugal" >Portugal</option><option value="Qatar" >Qatar</option><option value="Romania" >Romania</option><option value="Russia" >Russia</option><option value="Rwanda" >Rwanda</option><option value="Saint Kitts and Nevis" >Saint Kitts and Nevis</option><option value="Saint Lucia" >Saint Lucia</option><option value="Saint Vincent" >Saint Vincent</option><option value="Samoa" >Samoa</option><option value="San Marino" >San Marino</option><option value="Sao Tome and Principe" >Sao Tome and Principe</option><option value="Saudi Arabia" >Saudi Arabia</option><option value="Senegal" >Senegal</option><option value="Serbia and Montenegro" >Serbia and Montenegro</option><option value="Seychelles" >Seychelles</option><option value="Sierra Leone" >Sierra Leone</option><option value="Singapore" >Singapore</option><option value="Slovakia" >Slovakia</option><option value="Slovenia" >Slovenia</option><option value="Solomon Islands" >Solomon Islands</option><option value="Somalia" >Somalia</option><option value="South Africa" >South Africa</option><option value="Spain" >Spain</option><option value="Sri Lanka" >Sri Lanka</option><option value="Sudan" >Sudan</option><option value="Suriname" >Suriname</option><option value="Swaziland" >Swaziland</option><option value="Sweden" >Sweden</option><option value="Switzerland" >Switzerland</option><option value="Syria" >Syria</option><option value="Taiwan" >Taiwan</option><option value="Tajikistan" >Tajikistan</option><option value="Tanzania" >Tanzania</option><option value="Thailand" >Thailand</option><option value="Togo" >Togo</option><option value="Tonga" >Tonga</option><option value="Trinidad and Tobago" >Trinidad and Tobago</option><option value="Tunisia" >Tunisia</option><option value="Turkey" >Turkey</option><option value="Turkmenistan" >Turkmenistan</option><option value="Tuvalu" >Tuvalu</option><option value="Uganda" >Uganda</option><option value="Ukraine" >Ukraine</option><option value="United Arab Emirates" >United Arab Emirates</option><option value="United Kingdom" >United Kingdom</option><option value="United States" >United States</option><option value="Uruguay" >Uruguay</option><option value="Uzbekistan" >Uzbekistan</option><option value="Vanuatu" >Vanuatu</option><option value="Vatican City" >Vatican City</option><option value="Venezuela" >Venezuela</option><option value="Vietnam" >Vietnam</option><option value="Yemen" >Yemen</option><option value="Zambia" >Zambia</option><option value="Zimbabwe" >Zimbabwe</option></select>
                                      </label>
                                       </td></tr>
                                      </table>
                                  </label></p>

                                  <p><label for="email">Contact : <span class="w3-text-red">*</span>
                                    <br><input class="w3-input" id="email" name="email" placeholder="E-mail:" type="email"  value="">
                                  <input id="phone" name="phone" placeholder="T&eacute;l&eacute;phone" type="tel" value=""></label></p>

                                  <p><label for="birthdate">Date de naissance <span class="w3-text-red">*</span>
                                    <br><input class="w3-input date" id="birthdate" name="birthdate" type="text" value="">
                                  </label></p>

                                  <label for="status">Statut <span class="w3-text-red">*</span><br>
                                      <input class="w3-input" type="radio" name="status" value="Citizen" checked>Citoyen<br>
                                      <input class="w3-input" type="radio" name="status" value="Resident">Résident Permanent<br>
                                      <input class="w3-input" type="radio" name="status" value="Other" onchange="">Autre   <div id="idDivOtherStatus" class="w3-hide"><input id="otherstatus" name="otherstatus" placeholder="Autres status" type="text" value=""></div>
                                  </label>


                                  <label for="gender">Statut <span class="w3-text-red">*</span><br>
                                      <input class="w3-input" type="radio" name="gender" value="male">Homme<br>
                                      <input class="w3-input" type="radio" name="gender" value="female">Femme<br>
                                   </label>
                                   <div class="w3-center w3-margin-bottom">
                                        <button class="w3-button w3-green button-next-page">Prochaine Page</button>
                                   </div>
                                </div>
                                </div>
                           </div> <!-- end of form-->


                           <!-- Deuxieme page du formulaire -->
                           <div id="idDivJudoCanadaInformation" class="w3-container" style="display:none">
                                <div class="w3-card-4 ">

                                <div class="w3-container w3-green">
                                  <h2>Informations de Judo</h2>
                                </div>
                                <div class="w3-container">
                               <p><label for="name">Candidat pour : <span class="w3-text-red">*</span> <br>
                                      <select id="selectlevel" name="selectlevel">
                                          <option value="Shodan" selected="selected">Shodan</option>
                                          <option value="Nidan" >Nidan</option>
                                          <option value="Sandan" >Sandan</option>
                                          <option value="Yondan" >Yondan</option>
                                          <option value="Godan" >Godan</option>
                                          <option value="Rokudan" >Rokudan</option>
                                          <option value="Shichidan" >Shichidan</option>
                                          <option value="Hachidan" >Hachidan</option>
                                          <option value="Kudan" >Kudan</option></select>
                                  </label></p>
                                <p><input id="judocanada_id" name="judocanada_id" class="w3-input" placeholder="# de membre - Judo Canada" type="text"  value="" pattern="[0-9]*"></p>
                                <p><input id="dojo" name="dojo" class="w3-input" placeholder="Dojo" type="text" value=""></p>
                                <p><input id="instructor_name" name="instructor_name" class="w3-input" placeholder="Instructeur - Dan" type="text"  value=""></p>
                                <p><input id="instructor_email" name="instructor_email" class="w3-input" placeholder="Instructeur email" type="email"  value=""></p>
                                 <p><label for="enrollementdate">Date de début <span class="w3-text-red">*</span>
                                    <br><input class="w3-input date" id="enrollement_date" name="enrollement_date" type="text" value="">
                                  </label></p>
                                 <p><label for="startingdate">Année de début du judo <span class="w3-text-red">*</span>
                                    <br><input id="starting_date" name="starting_date" class="w3-input" type="number" min="1900" max="2020"  value="1990">
                                  </label></p>
                                  <p><label for="activeyear">Années actif en judo <span class="w3-text-red">*</span>
                                  <div id="divYearActive">
                                  </div>
                                  </label></p>
                                  <p>
                                  <label for="startingdate">Instructeurs précédents <span class="w3-text-red">*</span>
                                  <div id="input_instructor_wrapper">
                                        <button class="add_field_button w3-button">Ajouter un instructeur</button>
                                        <div class="duplicatable"><input type="text" name="instructors[]"></div>
                                    </div>
                                  </label></p>

                                <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>
                                  </div>
                               </div>

                                </div>
                           </div> <!-- end of form-->


                            <!-- troisieme page du formulaire -->
                            <div id="idDivCertification" class="w3-container" style="display:none">
                                <div class="w3-card-4 ">

                                <div class="w3-container w3-green">
                                  <h2>Informations de Certification</h2>
                                </div>
                                <div class="w3-container">
                               <p><label for="name">Grade - Date obtenue : <span class="w3-text-red">*</span> <br>
                                     <label>1K</label><input id="grade1K" name="grade1K" class="w3-input date" type="text"  value=""><br>
                                     <label>1D</label><input id="grade1D" name="grade1D" class="w3-input date" type="text" value=""><br>
                                     <label>2D</label><input id="grade2D" name="grade2D" class="w3-input date" type="text" value=""><br>
                                     <label>3D</label><input id="grade3D" name="grade3D" class="w3-input date" type="text" value=""><br>
                                     <label>4D</label><input id="grade4D" name="grade4D" class="w3-input date" type="text" value=""><br>
                                     <label>5D</label><input id="grade5D" name="grade5D" class="w3-input date" type="text" value=""><br>
                                     <label>6D</label><input id="grade6D" name="grade6D" class="w3-input date" type="text" value=""><br>
                                     <label>7D</label><input id="grade7D" name="grade7D" class="w3-input date" type="text" value=""><br>
                                     <label>8D</label><input id="grade8D" name="grade8D" class="w3-input date" type="text" value=""><br>
                               </label></p>
                               <p><label for="name">Certification d'arbitre - Date Obtenue <span class="w3-text-red">*</span> <br>
                                     <label>Reg</label><input id="grade_reg" name="grade_reg" class="w3-input date" type="text" value=""><br>
                                     <label>Prv C</label><input id="grade_prvc" name="grade_prvc" class="w3-input date" type="text" value=""><br>
                                     <label>Prv B</label><input id="grade_prvb" name="grade_prvb" class="w3-input date" type="text" value=""><br>
                                     <label>Prv A</label><input id="grade_prva" name="grade_prva" class="w3-input date" type="text" value=""><br>
                                     <label>Nat C</label><input id="grade_natc" name="grade_natc" class="w3-input date" type="text" value=""><br>
                                     <label>Nat B</label><input id="grade_natb" name="grade_natb" class="w3-input date" type="text" value=""><br>
                                     <label>Nat A</label><input id="grade_nata" name="grade_nata" class="w3-input date" type="text" value=""><br>
                                     <label>PJU</label><input id="grade_pju" name="grade_pju" class="w3-input date" type="text" value=""><br>
                                     <label>IJF</label><input id="grade_ifj" name="grade_ifj" class="w3-input date" type="text" value=""><br>
                                     <label>Other</label><input id="grade_other" name="grade_other" class="w3-input" type="text" value=""><br>
                               </label></p>
                               <p><label for="name">NCCP PNCE <span class="w3-text-red">*</span> <br>
                                     <label>HP Coach Certified</label><input id="grade_NCCPHPCoach" name="grade_NCCPHPCoach" class="w3-input date" type="text" value=""><br>
                                     <label>DA or Community Coach Certified</label><input id="grade_NCCPDA" name="grade_NCCPDA" class="w3-input date" type="text" value=""><br>
                                     <label>DI</label><input id="grade_NCCPDI" name="grade_NCCPDI" class="w3-input date" type="text" value=""><br>
                                     <label>CDev</label><input id="grade_NCCPCDev" name="grade_NCCPCDev" class="w3-input date" type="text" value=""><br>
                                     <label>IV</label><input id="grade_NCCPIV" name="grade_NCCPIV" class="w3-input date" type="text" value=""><br>
                                     <label>V</label><input id="grade_NCCPV" name="grade_NCCPV" class="w3-inputdate " type="text" value=""><br>
                               </label></p>

                                <p><label for="name">Facilitateur de cours <span class="w3-text-red">*</span> <br>
                                     <label>DA</label><input id="grade_facilitator_DA" name="grade_facilitator_DA" class="w3-input date" type="text" value=""><br>
                                     <label>DI</label><input id="grade_facilitator_DI" name="grade_facilitator_DI" class="w3-input date" type="text" value=""><br>
                                     <label>CDev</label><input id="grade_facilitator_Dev" name="grade_facilitator_Dev" class="w3-input date" type="text" value=""><br>
                                     <label>IV</label><input id="grade_facilitator_IV" name="grade_facilitator_IV" class="w3-input date" type="text" value=""><br>
                                     <label>V</label><input id="grade_Facilitator_V" name="grade_Facilitator_V" class="w3-input date" type="text" value=""><br>
                               </label></p>

                                  <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>
                                  </div>
                               </div>
                               </div>
                           </div> <!-- end of form-->


                            <!-- quatrieme page du formulaire -->
                            <div id="idDivGrade" class="w3-container w3-margin-left w3-card-4" style="display:none">

                                <div class="w3-container w3-green">
                                  <h2>Point en tournois</h2>
                                </div>
                                <div class="w3-container">
                                  <div class="w3-container w3-modal" id="msgBoxPoint1">
                                    <div class="w3-container w3-card-4 w3-modal-content" style="position: sticky;top: 50%;left: 50%; transform: translate(-50%, -50%);">
                                    <div id="rules_tournament"></div>
                                    <button onclick="$('#msgBoxPoint1').fadeOut();" class="w3-button w3-grey">Fermer</button>
                                    </div>
                                  </div>
                                        <button onclick="$('#msgBoxPoint1').fadeIn();" class="w3-button w3-grey">Voir les reglements</button>
                                    </div>
                                  
                                  <div id="input_point_system_wrapper">
                                        <div class="duplicatable"> <p><label for="startingdate">Système de pointage : <span class="w3-text-red">*</span>
                                      <br><input class="w3-input date" id="grade_date" name="grade_date[]" type="text" value="" onfocus="$('input.date').dcalendarpicker();" placeholder="Date du tournois">
                                      <p><select name="grade_type[]" id="grade_type">
                                          <option value="shiai">Shiai</option>
                                          <option value="kata">Kata</option>
                                          </select>
                                      </p>
                                      <br><input class="w3-input" id="contest_location" name="contest_location[]" placeholder="Tournoi et Lieu" type="text" value="">
                                      <br><input class="w3-input" id="adversary" name="adversary[]" placeholder="Adversaire/Partenaire (Uke/Tori)" type="text" value="">
                                      <br><input class="w3-input" id="grade" name="grade[]" placeholder="Grade" type="text" value="">
                                      <br><input class="w3-input" id="kata" name="kata[]" placeholder="Kata/Paires" type="text" value="">
                                      <br><input class="w3-input" id="results" name="results[]" placeholder="Resultats" type="text" value="">
                                      <!--<br><input class="w3-input" id="participation" name="participation[]" placeholder="Points de Participation" type="text" value="">-->
                                      <br><input class="w3-input" id="points" name="points[]" placeholder="Points" type="number" value="">
                                      </label></p> </div>
                                      <button class="add_field_button w3-button">Ajouter</button>
                                    </div>




                                  <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>
                                  </div>
                               </div>
                              
                           </div> <!-- end of form-->

                            <!-- cinquieme page du formulaire -->
                            <div id="idDivTechnicalPoint" class="w3-container" style="display:none">
                                <div class="w3-card-4 ">

                                <div class="w3-container w3-green">
                                  <h2>Points techniques/non-techniques</h2>
                                </div>
                                <div class="w3-container">
                                  <div class="w3-container w3-modal" id="msgBoxPoint2">
                                    <div class="w3-container w3-card-4 w3-modal-content" style="position: sticky;">
                                        <div id="rule_technical_points"></div>
                                        <button onclick="$('#msgBoxPoint2').fadeOut();" class="w3-button w3-grey">Fermer</button>
                                    </div>
                                  </div>
                                        <button onclick="$('#msgBoxPoint2').fadeIn();" class="w3-button w3-grey">Voir les reglements</button>
                                     <div id="input_point_system_wrapper2">
                                        <div class="duplicatable">  <p><label for="name">Points : <span class="w3-text-red">*</span>
                                      <br><select class="w3-input" id="grade_code2" name="grade_code2[]">
                                          <option value="DA" category="T1">Certification PNCE DA</option>
                                          <option value="DI" category="T1">Certification PNCE DI</option>
                                          <option value="CDev" category="T1">Certification PNCE CDev</option>
                                          <option value="IV" category="T1">Certification PNCE IV</option>
                                          <option value="V" category="T1">Certification PNCE V</option>
                                          <option value="DA" category="T2">Entraîneur PNCE DA</option>
                                          <option value="DI" category="T2">Entraîneur PNCE DI</option>
                                          <option value="CDev" category="T2">Entraîneur PNCE CDev</option>
                                          <option value="IV" category="T2">Entraîneur PNCE IV</option>
                                          <option value="V" category="T2">Entraîneur PNCE V</option>
                                          <option value="T9" category="T9">Développement de club - Sensei</option>
                                          <option value="Prov" category="T3">Directeur de Clinique Prov</option>
                                          <option value="InterProv" category="T3">Directeur de Clinique InterProv</option>
                                          <option value="Int" category="T3">Directeur de Clinique Int</option>
                                          <option value="Prov" category="T4"> Participant aux cliniques Prov</option>
                                          <option value="Nat" category="T4"> Participant aux cliniques Nat</option>
                                          <option value="Int" category="T4"> Participant aux cliniques Int</option>
                                          <option value="Prov" category="T5"> Certification d'arbitre Prov</option>
                                          <option value="Nat" category="T5"> Certification d'arbitre Nat</option>
                                          <option value="Int" category="T5"> Certification d'arbitre Int</option>
                                          <option value="Prov" category="T6"> Arbitrage Prov</option>
                                          <option value="Nat" category="T6"> Arbitrage Nat</option>
                                          <option value="Int" category="T6"> Arbitrage Int</option>
                                           <option value="Prov" category="T7"> Certification de kata Prov</option>
                                          <option value="Nat" category="T7"> Certification de kata Nat</option>
                                          <option value="Cont" category="T7"> Certification de kata Cont</option>
                                          <option value="Int" category="T7"> Certification de kata Int</option>
                                          <option value="Prov" category="T8"> Activité de Kata Prov</option>
                                          <option value="InterProv" category="T8"> Activité de Kata InterProv</option>
                                          <option value="Nat" category="T8"> Activité de Kata kata Nat</option>
                                          <option value="Int" category="T8"> Activité de Kata kata Int</option>
                                           <option value="Prov" category="N2"> Bénévole de tournoi Prov</option>
                                          <option value="InterProv" category="N2"> Bénévole de tournoi InterProv</option>
                                          <option value="Nat" category="N2"> Bénévole de tournoi Nat</option>
                                          <option value="Int" category="N2"> Bénévole de tournoi Int</option>
                                          </select>
                                      <br><input class="w3-input date" id="grade_date2" name="grade_date2[]" type="text" placeholder="Date de l'événement" value="" onfocus="$('input.date').dcalendarpicker();">
                                      <br><input class="w3-input" id="grade_contest_name2" name="grade_contest_name2[]" placeholder="Tournoi" type="text" value="">
                                      <br><input class="w3-input" id="grade_contest_location2" name="grade_contest_location2[]" placeholder="Lieu" type="text" value="">
                                      <br><input class="w3-input" id="grade_contest_position2" name="grade_contest_position2[]" placeholder="Position" type="text" value="">
                                      <br><input class="w3-input" id="grade_contest_level2" name="grade_contest_level2[]" placeholder="Niveau" type="text" value="">
                                      </label></p> </div>
                                      <button class="add_field_button w3-button" >Ajouter</button>
                                    </div>

                                  <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>
                                  </div>
                               </div>
                               </div>
                           </div> <!-- end of form-->

                           <div id="idDivFinalPoint" class="w3-container w3-margin-left w3-card-4" style="display:none">

                                <div class="w3-container w3-green">
                                  <h2>Sommaire des points</h2>
                                </div>
                                <div class="w3-container">
                                  <div id="idPointTableSummary">
                                  </div>
                                  <p><label for="name">Si vous avez d'autres points à ajouter, veuillez les énumérer ci-dessous<span class="w3-text-red">*</span> </label></p><br>
                                  <textarea rows="4" cols="50" name="additional_points">

                                  </textarea>
                                  <p>Total Technical/Competitive Points : <span id="total_tc_points"></span></p>
                                   <p>Grand Total : <span id="total_points"></span></p>


                                  <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>
                                  </div>
                               </div>
                              
                           </div> <!-- end of form-->

                           <div id="idDivIJFOnly" class="w3-container w3-margin-left w3-card-4" style="display:none">

                                <div class="w3-container w3-green">
                                  <h2>IJF SEULEMENT</h2>
                                </div>
                                <div class="w3-container">
                                  <div id="idPointTableSummary">
                                  </div>
                                  <input type="text" placeholder="Certificat de Dan PJC demandé" />
                                  <input type="text" placeholder="Certificat de Dan IJF demandé" />
                                  <p><label>Date de promotion des dan
                                        <div id = "idDivPromotionDanInput">
                                  </div>
                                    </label></p>


                                  <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>

                                  </div>
                               </div>
                              
                           </div> <!-- end of form-->
                            <div id="idDivFinalPoint" class="w3-container w3-margin-left w3-card-4" style="display:none">

                                <div class="w3-container w3-green">
                                  <h2>Sommaire des points</h2>
                                </div>
                                <div class="w3-container">
                                  <div id="idPointTableSummary">
                                  </div>
                                  <p><label for="name">Si vous avez d'autres points à ajouter, veuillez les énumérer ci-dessous<span class="w3-text-red">*</span> <br>
                                  <textarea rows="4" cols="50">

                                  </textarea>
                                  <input type="text" placeholder="Total Technical/Competitive Points" />
                                  <input type="text" placeholder="Grand Total" />


                                  <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>
                                  </div>
                               </div>
                              
                           </div> <!-- end of form-->

                           <div id="idDivPayForm" class="w3-container w3-margin-left w3-card-4" style="display:none">

                                <div class="w3-container w3-green">
                                  <h2>Payments</h2>
                                </div>
                                <div class="w3-container">
                                  <p><label>Frais de grade
                                        <div id = "idDivPromotionDanInput">
                                  </div>
                                    </label></p>


                                  <div class="w3-center w3-margin-bottom">
                                    <button class="w3-button w3-grey button-previous-page">Page précédante</button>
                                    <button class="w3-button w3-green button-next-page">Prochaine Page</button>

                                  </div>
                               </div>
                              
                           </div> <!-- end of form-->


            <?php while ( have_posts() ) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                            

                        </div> .entry-content -->

                    </article><!-- #post -->

            <?php endwhile; // end of the loop. ?>

        </div><!-- #content -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>