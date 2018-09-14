const pages = ["idDivFormPersonalInformations",
                        "idDivJudoCanadaInformation",
                        "idDivCertification",
                        "idDivGrade",
                        "idDivTechnicalPoint",
                        "idDivFinalPoint",
                        "idDivIJFOnly",
                        "idDivPayForm"
                        ];
        const yearMin = 2010;
        const labels = [{label:"Actif en judo", type:"year_active"},
                        {label:"Tournois de kata", type:"tournois_kata"},
                        {label:"Participation en kata", type:"participation_kata"},
                        {label:"Tournois en shiai", type:"tournois_shiai"},
                        {label:"Participation en Shiai", type:"participation_shiai"},
                        {label:"Certification PNCE", type:""},
                        {label:"Directeur Technique", type:"T9"},
                        {label:"Assistant Entraîneur", type:"T2"},
                        {label:"Entraîneur", type:"T2"},
                        {label:"Directeurs de clinique", type:"T3"},
                        {label:"Participant aux cliniques", type:"T4"},
                        {label:"Certification en kata", type:"T7"},
                        {label:"Évaluation en kata", type:"T8"},
                        {label:"Certification d'arbitre", type:"T5"},
                        {label:"Arbitrage", type:"T7"},
                        {label:"Bénévole de tournoi", type:"N2"}];
        const point_category_order = []
        const pointYearActive = {
            "":0,
            "Ikkyu":30,
            "Shodan":20,
            "Nidan":20,
            "Sandan":10,
            "Superieure":10
        };
        const pointTechniques ={
          "T1":{
              MAX:30,
              DA: 5,
              DI: 10,
              CDev:20,
              IV:20,
              V:20
          },
          "T2":{
              MAX:30,
              DA: 5,
              DI: 10,
              CDev:20,
              IV:20,
              V:20
          },
          "T3":{
              MAX:30,
              Prov:10,
              InterProv: 15,
              Nat: 15,
              Int:20
          },
         "T4":{
              MAX:20,
              Prov:5,
              Nat: 5,
              Int: 20
          },
          "T5":{
              MAX:1000,
              Prov:10,
              Nat: 15,
              Int: 20
          },
          "T6":{
              MAX:60,
              Prov:5,
              Nat: 10,
              Int: 20
          },
          "T7":{
              MAX:1000,
              Prov:10,
              Nat: 15,
              Cont: 15,
              Int: 20
          },
            "T8":{
              MAX:30,
              Prov:5,
              InterProv: 10,
              Nat: 15,
              Int: 20
          },
          "T9":{
              MAX:1000,
              T9:30
          },
          "N2":{
              MAX:10,
              Prov:3,
              InterProv: 4,
              Nat: 5,
              Int: 5
          }
        };
        const rules_tournament = "<h3>REGISTRE DES POINTS SHIAI ET KATA</h3><p><small><strong>SHIAI</strong><br>Ippon = 10 pts<br>Wazari = 7 pts <br><strong>KATA</strong> <br>Les points seront attribués à 2 points de moins que le classement de leurs équipe.<br><strong>KATA et SHIAI</strong><br>5 points pour participation <br></small></p>";
        const rules_technical_points = "<h3>REGISTRE DES POINTS TECHNIQUE ET NON-TECHNIQUE</h3><p> <small><b>POINTS TECHNIQUE</b><br>Certification PNCE (Code T1) (MAXIMUM DE 30pts/année)<br>DA - 5 points<br>DI - 10 points<br>CDev - 20 points<br>IV - 20 points<br>V - 20 points<br>Entraîneur (PNCE Certifié avec min. de 120h/année) (Code T2) (MAXIMUM DE 30pts/année)<br>DA - 5 points<br>DI - 10 points<br>CDev - 20 points<br>IV - 20 points<br>V - 20 points<br>Développement de club - Sensei - minimum de 25 membres (Code T9)<br>30 points/année<br>Directeur de Clinique (Code T3) (MAXIMUM DE 30pts/année)<br>Prov - 10<br>InterProv - 15 <br>Nat - 15<br>Int\'l - 20<br>Participant aux cliniques (Code T4) (MAXIMUM DE 20pts/année)<br>Prov - 5<br>Nat - 5<br>Int\'l - 5<br>Certification d\'arbitre (Code T5)<br>Prov - 10<br>Nat - 15<br>Int\'l - 20/20/20<br>Arbitrage (Code T6) (MAXIMUM DE 60pts/année)<br>Prov - 5 (MAXIMUM DE 25pts/année)<br>Nat - 10 (MAXIMUM DE 20pts/année)<br>Int\'l - 20<br>Certification de kata (Code T7)<br>Prov - 10<br>Nat - 15<br>Cont - 15<br>Int\'l - 20/20/20<br>Activité de Kata (Code T8) (MAXIMUM DE 30pts/année)<br>Prov - 5<br>InterProv - 10<br>Nat - 15<br>Int\'l - 20<br><br><b>POINTS NON-TECHNIQUE</b><br>Actif en judo (Code N1)<br>1kyu - 30 <br>1D/2D - 20 <br>3D+ - 10  <br>Bénévole de tournoi (Code N2) (MAXIMUM DE 10pts/année) <br>Prov - 3 <br>InterProv - 4 <br>Nat - 5 <br>Int\'l - 5 <br> </small></p>";
        const prices =[[
                    {type:"Shodan", prix:"275"},
                    {type:"Nidan", prix:"275"},
                    {type:"Sandan", prix:"275"},
                    {type:"Yondan", prix:"275"},
                    {type:"Godan", prix:"275"},
                    {type:"Rokudan", prix:"275"},
                    {type:"Shichidan", prix:"275"},
                    {type:"Hachidan", prix:"275"},
                    {type:"Kudan", prix:"275"},
                    {type:"Replacement Diploma", prix:"35"}
                      ],[
                    {type:"Shodan", prix:"100"},
                    {type:"Nidan", prix:"125"},
                    {type:"Sandan", prix:"150"},
                    {type:"Yondan", prix:"220"},
                    {type:"Godan", prix:"325"},
                    {type:"Rokudan", prix:"575"},
                    {type:"Shichidan", prix:"700"},
                    {type:"Hachidan", prix:"950"},
                    {type:"Replacement Diploma", prix:"35"}
                     ]];
        const labelsPromotionDan = ["Dan - PJC", "Dan - IJF", "Dan - National"];