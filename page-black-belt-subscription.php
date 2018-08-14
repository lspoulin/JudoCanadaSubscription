<?php get_header(); ?>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <script src="<?php echo get_stylesheet_directory_uri().'/object.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/navigation.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/judo_info.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/country.js';?>"></script>
    <script>

        var pages = ["idDivFormPersonalInformations", "idDivJudoCanadaInformation", "idDivCertification", "idDivGrade", "idDivTechnicalPoint", "idDivFinalPoint", "idDivIJFOnly", "idDivPayForm"];
        
        var data = {};
        var index = 0;
        

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

        var labelsPromotionDan = ["Dan - PJC", "Dan - IJF", "Dan - National"];
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

            $(".button-next-page").click(function(){changePageUp();});
            $(".button-previous-page").click(function(){changePageDown();});
            instructorsInput.init();
            pointInput.init();
            pointInput2.init();
            sportResult.init();
            trainer.init();

            writeSelect();
            createTableSummaryPoint();
            createInputPromotionDan();
            createInputYearActive();

            initData();
            

        } );

        function createInputPromotionDan(){
          var html = "";
            for (var i = 1; i <= 9; i++)
              for (var j=0; j < 3 ; j++)
                html += "<input type='date' placeholder='"+i + ((i==1)?"er ":"ieme ") + labelsPromotionDan[j]+"'/>";

            $("#idDivPromotionDanInput").html(html);

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
                  var index = "idSelect"+(getCurrentYear()-(i+1));
                  html+="<td><select id=\""+index+"\" class=\"w3-input\" type=\"text\" name=\"\" ><option value=\"\" selected=\"selected\">";

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
              for (var i = 0 ; i <= year - yearMin + 1; i++){
                if (i==0){
                  html+="<td>" + labels[j] +"</td>";
                }
                else{
                  if(j==0){
                    var index = "idSelect"+(getCurrentYear()-(i+1));
                    var key = $("#"+index).val();
                    html+="<td>"+pointYearActive[key]+"</td>";
                  }
                    
                }
              }
            }

            html +="</tr></table>";

            $('#idPointTableSummary').html(html);
            $('#idTablePoints tr:odd').addClass("w3-grey");
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
                                      <br><table><tr><td><input class="w3-input" id="idFirstName" placeholder="Pr&eacute;nom" type="text" name="firstname" value="" required="required"> </td>
                                      <td><input class="w3-input" id="idName" placeholder="Nom" type="text" name="name" value="" required="required">  </td></tr></table>
                                  </label></p>

                                  <p><label for="adress">Votre adresse : <span class="w3-text-red">*</span>
                                    <table><tr><td colspan="2">
                                      <br><input class="w3-input" id="idStreetAdress2" placeholder="Adresse" type="text" name="adress1" value=""></td></tr>
                                      <tr><td colspan="2">
                                      <br><input class="w3-input" id="idStreetAdress2" placeholder="Adresse line 2" type="text" name="adress2" value=""></td></tr>
                                      <tr><td>
                                      <br><input class="w3-input" id="idCity" placeholder="Ville" type="text" name="city" value=""> </td><td>
                                      <br><input class="w3-input" id="idState" placeholder="Etat" type="text" name="state" value=""> </td></tr><tr><td>
                                      <br><input class="w3-input" id="idZipCode" placeholder="Code postal" type="text" name="zipcode" value=""> </td><td>

                                      <br><label for="adress">Pays:<br><div id='idSelectCountry'>test</div>
                                      </label>
                                       </td></tr>
                                      </table>
                                  </label></p>

                                  <p><label for="email">Contact : <span class="w3-text-red">*</span>
                                    <br><input class="w3-input" id="idEmail" placeholder="E-mail:" type="email" name="email" value="">
                                  <input id="idPhone" placeholder="T&eacute;l&eacute;phone" type="tel" name="phone" value=""></label></p>

                                  <p><label for="birthdate">Date de naissance <span class="w3-text-red">*</span>
                                    <br><input class="w3-input" id="idBirthdate" type="date" name="birthdate" value="">
                                  </label></p>

                                  <label for="status">Statut <span class="w3-text-red">*</span><br>
                                      <input class="w3-input" type="radio" name="statusCitizen" value="Citizen" checked>Citoyen<br>
                                      <input class="w3-input" type="radio" name="statusPermanent" value="Resident">Résident Permanent<br>
                                      <input class="w3-input" type="radio" value="Other" onchange="">Autre   <div id="idDivOtherStatus" class="w3-hide"><input id="idOtherStatus" placeholder="Autres status" type="text" name="otherstatus" value=""></div>
                                  </label>


                                  <label for="gender">Statut <span class="w3-text-red">*</span><br>
                                      <input class="w3-input" type="radio" name="genderMale" value="male">Homme<br>
                                      <input class="w3-input" type="radio" name="genderFemale" value="female">Femme<br>
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
                                      <select id="idSelectLevel"><option value="Shodan" selected="selected">Shodan</option>
                                                                    <option value="Nidan" >Nidan</option>
                                                                    <option value="Sandan" >Sandan</option>
                                                                    <option value="Yondan" >Yondan</option>
                                                                    <option value="Godan" >Godan</option>
                                                                    <option value="Rokudan" >Rokudan</option>
                                                                    <option value="Shichidan" >Shichidan</option>
                                                                    <option value="Hachidan" >Hachidan</option>
                                                                    <option value="Kudan" >Kudan</option></select></label></p>
                                <p><input id="idJudoCanadaId" class="w3-input" placeholder="# de membre - Judo Canada" type="text" name="message_judocanada_id" value="" pattern="[0-9]*"></p>
                                <p><input id="idDojo" class="w3-input" placeholder="Dojo" type="text" name="message_dojo" value=""></p>
                                <p><input id="idInstructor" class="w3-input" placeholder="Instructeur - Dan" type="text" name="message_instructor" value=""></p>
                                <p><input id="idInstructorEmail" class="w3-input" placeholder="Instructeur email" type="email" name="message_instructor_email" value=""></p>
                                 <p><label for="enrollementdate">Date de début <span class="w3-text-red">*</span>
                                    <br><input class="w3-input" id="idEnrollementDate" type="date" name="message_enrollementdate" value="">
                                  </label></p>
                                 <p><label for="startingdate">Année de début du judo <span class="w3-text-red">*</span>
                                    <br><input class="w3-input" id="idStartingDate" type="number" min="1900" max="2020" name="message_startingdate" value="1990">
                                  </label></p>
                                  <p><label for="startingdate">Années actif en judo <span class="w3-text-red">*</span>
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
                                     <label>1K</label><input id="idGrade1K" class="w3-input" type="date" name="message_grade1k" value=""><br>
                                     <label>1D</label><input id="idGrade1D" class="w3-input" type="date" name="message_grade1d" value=""><br>
                                     <label>2D</label><input id="idGrade2D" class="w3-input" type="date" name="message_grade2d" value=""><br>
                                     <label>3D</label><input id="idGrade3D" class="w3-input" type="date" name="message_grade3d" value=""><br>
                                     <label>4D</label><input id="idGrade4D" class="w3-input" type="date" name="message_grade4d" value=""><br>
                                     <label>5D</label><input id="idGrade5D" class="w3-input" type="date" name="message_grade5d" value=""><br>
                                     <label>6D</label><input id="idGrade6D" class="w3-input" type="date" name="message_grade6d" value=""><br>
                                     <label>7D</label><input id="idGrade7D" class="w3-input" type="date" name="message_grade7d" value=""><br>
                                     <label>8D</label><input id="idGrade8D" class="w3-input" type="date" name="message_grade8d" value=""><br>
                               </label></p>
                               <p><label for="name">Certification d'arbitre - Date Obtenue <span class="w3-text-red">*</span> <br>
                                     <label>Reg</label><input id="idGradeReg" class="w3-input" type="date" name="message_gradeReg" value=""><br>
                                     <label>Prv C</label><input id="idGradePrvC" class="w3-input" type="date" name="message_gradePrvC" value=""><br>
                                     <label>Prv B</label><input id="idGradePrvB" class="w3-input" type="date" name="message_gradePrvB" value=""><br>
                                     <label>Prv A</label><input id="idGradePrvA" class="w3-input" type="date" name="message_gradePrvA" value=""><br>
                                     <label>Nat C</label><input id="idGradeNatC" class="w3-input" type="date" name="message_gradeNatC" value=""><br>
                                     <label>Nat B</label><input id="idGradeNatB" class="w3-input" type="date" name="message_gradeNatB" value=""><br>
                                     <label>Nat A</label><input id="idGradeNatA" class="w3-input" type="date" name="message_gradeNatA" value=""><br>
                                     <label>PJU</label><input id="idGradePJU" class="w3-input" type="date" name="message_gradePJU" value=""><br>
                                     <label>IJF</label><input id="idGradeIJF" class="w3-input" type="date" name="message_gradeIJF" value=""><br>
                                     <label>Other</label><input id="idGradeOther" class="w3-input" type="text" name="message_gradeOther" value=""><br>
                               </label></p>
                               <p><label for="name">NCCP PNCE <span class="w3-text-red">*</span> <br>
                                     <label>HP Coach Certified</label><input id="idGradeNCCPHPCoach" class="w3-input" type="text" name="message_gradeNCCPHPCoach" value=""><br>
                                     <label>DA or Community Coach Certified</label><input id="idGradNCCPDA" class="w3-input" type="date" name="message_gradeNCCPDA" value=""><br>
                                     <label>DI</label><input id="idGradeNCCPDI" class="w3-input" type="date" name="message_gradeNCCPDI" value=""><br>
                                     <label>CDev</label><input id="idGradeNCCPCDev" class="w3-input" type="date" name="message_gradeNCCPCDev" value=""><br>
                                     <label>IV</label><input id="idGradeNCCPIV" class="w3-input" type="date" name="message_gradeNCCPIV" value=""><br>
                                     <label>V</label><input id="idGradeNCCPV" class="w3-input" type="date" name="message_gradeNCCPV" value=""><br>
                               </label></p>

                                <p><label for="name">Facilitateur de cours <span class="w3-text-red">*</span> <br>
                                     <label>DA</label><input id="idGradFacilitatorDA" class="w3-input" type="date" name="message_gradeFacilitatorDA" value=""><br>
                                     <label>DI</label><input id="idGradeFacilitatorDI" class="w3-input" type="date" name="message_gradeFacilitatorDI" value=""><br>
                                     <label>CDev</label><input id="idGradeFacilitatorDev" class="w3-input" type="date" name="message_gradeFacilitatorDev" value=""><br>
                                     <label>IV</label><input id="idGradeFacilitatorIV" class="w3-input" type="date" name="message_gradeFacilitatorIV" value=""><br>
                                     <label>V</label><input id="idGradeFacilitatorV" class="w3-input" type="date" name="message_gradeFacilitatorV" value=""><br>
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
                                  <h2>Formulaire de grade</h2>
                                </div>
                                <div class="w3-container">
                                  <div class="w3-container w3-modal w3-display-middle" id="msgBoxPoint1">
                                    <div class="w3-container w3-card-4 w3-modal-content"><h3>REGISTRE DES POINTS SHIAI ET KATA</h3>
                                    <p><small>
                                    <strong>SHIAI</strong><br>
                                    Ippon = 10 pts<br>
                                    Wazari = 7 pts <br>

                                    <strong>KATA</strong> <br>
                                    Les points seront attribués à 2 points de moins que le classement de leurs équipe.<br>

                                    <strong>KATA et SHIAI</strong><br>
                                    5 points pour participation <br>
                                    </small></p>
                                    <button onclick="$('#msgBoxPoint1').fadeOut();" class="w3-button w3-grey">Fermer</button>
                                    </div>
                                    </div>
                                        <button onclick="$('#msgBoxPoint1').fadeIn();" class="w3-button w3-grey">Voir les reglements</button>
                                    </div>
                                  
                                  <div id="input_point_system_wrapper">
                                        <div class="duplicatable"> <p><label for="startingdate">Système de pointage : <span class="w3-text-red">*</span>
                                      <br><input class="w3-input" id="idGradeDate" type="DATE" name="message_gradedate" value="">
                                      <br><input class="w3-input" id="idContest" placeholder="Tournoi et Lieu" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Adversaire/Partenaire (Uke/Tori)" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Grade" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Kata/Paires" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Resultats" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Points de Participation" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Points" type="text" name="message_contest" value="">
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
                                  <h2>Formulaire de grade</h2>
                                </div>
                                <div class="w3-container">
                                  <div class="w3-container w3-modal w3-display-middle" id="msgBoxPoint2">
                                    <div class="w3-container w3-card-4 w3-modal-content" ><h3>REGISTRE DES POINTS TECHNIQUE ET NON-TECHNIQUE</h3>
                                      <p> <small>
                                        <b>POINTS TECHNIQUE</b><br>
                                        Certification PNCE (Code T1) (MAXIMUM DE 30pts/année)<br>
                                        DA - 5 points<br>
                                        DI - 10 points<br>
                                        CDev - 20 points<br>
                                        IV - 20 points<br>
                                        V - 20 points<br>
                                        Entraîneur (PNCE Certifié avec min. de 120h/année) (Code T2) (MAXIMUM DE 30pts/année)<br>
                                        DA - 5 points<br>
                                        DI - 10 points<br>
                                        CDev - 20 points<br>
                                        IV - 20 points<br>
                                        V - 20 points<br>
                                        Développement de club - Sensei - minimum de 25 membres (Code T9)<br>
                                        30 points/année<br>
                                        Directeur de Clinique (Code T3) (MAXIMUM DE 30pts/année)<br>
                                        Prov - 10<br>
                                        InterProv - 15 <br>
                                        Nat - 15<br>
                                        Int'l - 20<br>
                                        Participant aux cliniques (Code T4) (MAXIMUM DE 20pts/année)<br>
                                        Prov - 5<br>
                                        Nat - 5<br>
                                        Int'l - 5<br>
                                        Certification d'arbitre (Code T5)<br>
                                        Prov - 10<br>
                                        Nat - 15<br>
                                        Int'l - 20/20/20<br>
                                        Arbitrage (Code T6) (MAXIMUM DE 60pts/année)<br>
                                        Prov - 5 (MAXIMUM DE 25pts/année)<br>
                                        Nat - 10 (MAXIMUM DE 20pts/année)<br>
                                        Int'l - 20<br>
                                        Certification de kata (Code T7)<br>
                                        Prov - 10<br>
                                        Nat - 15<br>
                                        Cont - 15<br>
                                        Int'l - 20/20/20<br>
                                        Activité de Kata (Code T8) (MAXIMUM DE 30pts/année)<br>
                                        Prov - 5<br>
                                        InterProv - 10<br>
                                        Nat - 15<br>
                                        Int'l - 20<br><br>

                                        <b>POINTS NON-TECHNIQUE</b><br>
                                        Actif en judo (Code N1)<br>
                                        1kyu - 30 <br>
                                        1D/2D - 20 <br>
                                        3D+ - 10  <br>
                                        Bénévole de tournoi (Code N2) (MAXIMUM DE 10pts/année) <br>
                                        Prov - 3 <br>
                                        InterProv - 4 <br>
                                        Nat - 5 <br>
                                        Int'l - 5 <br> </small>
                                        </p>
                                        <button onclick="$('#msgBoxPoint2').fadeOut();" class="w3-button w3-grey">Fermer</button>
                                    </div>
                                  </div>
                                        <button onclick="$('#msgBoxPoint2').fadeIn();" class="w3-button w3-grey">Voir les reglements</button>
                                     <div id="input_point_system_wrapper2">
                                        <div class="duplicatable">  <p><label for="name">Points : <span class="w3-text-red">*</span>
                                      <br><input class="w3-input" id="idContest" placeholder="Code" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idGradeDate" type="date" name="message_gradedate" value="">
                                      <br><input class="w3-input" id="idContest" placeholder="Tournoi" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Lieu" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Position" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Niveau" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Points technique" type="text" name="message_contest" value="">
                                      <br><input class="w3-input" id="idAdversary" placeholder="Points non-technique" type="text" name="message_contest" value="">
                                      </label></p> </div>
                                      <button class="add_field_button w3-button">Ajouter</button>
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