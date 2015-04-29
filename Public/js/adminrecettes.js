
// Création d'une recette
//tabUnit     = new Array();
//tabQuant    = new Array();


function ajouterIngredient(){
    var val=$( "#allingredients option:selected" ).val();
    var lab=$( "#allingredients option:selected" ).text();
    if($('#allingredients option').length!==0 ){
        $('#ingredients').append('<option value="'+val+'" selected="selected">'+lab+'</option>');
        $('#unites').append('<option value="'+val+'" selected="selected">...</option>');

        $('#allingredients option:selected').remove();
        tabUnit.push("rien");
        tabQuant.push(1);

    }
}

function retirerIngredient(){
    var val=$( "#ingredients option:selected" ).val();
    var lab=$( "#ingredients option:selected" ).text();
    if($('#ingredients option').length !== 0 ){

        //$('#unites option:eq(3)').prop('selected', true);
        var ing = document.getElementById("ingredients");
        var unit = document.getElementById("unites");

        //supprime fna le select
        unit.options[ing.selectedIndex].remove();
        
        //supprime dans quantité
        tabUnit.splice(ing.selectedIndex,1);
        tabQuant.splice(ing.selectedIndex,1);
  

        console.log(tabUnit +" # "+ tabQuant);

        $('#allingredients').append('<option value="'+val+'" selected="selected">'+lab+'</option>');
        $('#ingredients option:selected').remove();
    }
}


function definirUnite(){
    var allUnit = document.getElementById("allunites");
    var unit = document.getElementById("unites");
    if(unit.selectedIndex>=0 ){
        unit.options[unit.selectedIndex].value= allUnit.options[allUnit.selectedIndex].value ;
        unit.options[unit.selectedIndex].text= tabQuant[unit.selectedIndex]+" " + allUnit.options[allUnit.selectedIndex].text ;

        tabUnit[unit.selectedIndex]=allUnit.options[allUnit.selectedIndex].text;
        //console.log(tabQuant[unit.selectedIndex]);
        //tabQuant[unit.selectedIndex]=1;

        console.log(tabUnit+"##"+ tabQuant);
        //alert(allUnit.options[allUnit.selectedIndex].text); 
    }
}

function definirQuantite(){

    var quant = document.getElementById("quantite");
    var unit = document.getElementById("unites");
    if(parseFloat(quant.value)>0 ){
        
        unit.options[unit.selectedIndex].text= parseFloat(quant.value) + " " + tabUnit[unit.selectedIndex];

        tabQuant[unit.selectedIndex]=quant.value;
        console.log(tabUnit+"##"+ tabQuant);

        //alert(allUnit.options[allUnit.selectedIndex].text); 
    }



}


 
$( "form" ).submit(function( event ) {
    event.preventDefault();
    alors = false;
    for( i = 0; i < tabUnit.length; i++ ){
        if(tabUnit[i]==="rien"){
            alors = true;
        }
    }
        if(tabUnit.length == 0){
            alors = true;
        }



    if (alors) {    //verifie si chaque ingredient
        alert("veuillez selectionner une unité pour chaque ingredient");
        return false;
        
    }else{      //prepare l'envoi


        //selectionne tout
        $('#ingredients option').prop('selected', true);
        $('#unites option').prop('selected', true);


        var formulaire = document.getElementById("formRecette");
                    
        
         var inputQuant;
         console.log(tabQuant);

        for(i=0; i<tabQuant.length; i++){
            inputQuant = document.createElement("input");

            inputQuant.setAttribute("type","hidden");
            inputQuant.setAttribute("name","quantites["+i+"]");
            inputQuant.setAttribute("value",tabQuant[i]);
            formulaire.appendChild(inputQuant);
        }
 

            //recrée le bouton btn
            inputQuant2 = document.createElement("input");
            inputQuant2.setAttribute("type","hidden");
            inputQuant2.setAttribute("name","btn");
            formulaire.appendChild(inputQuant2 );


        formulaire.submit();

    }    
});




/*function preparatif(){
}*/



function actualiserQuantite(){
 var quant = document.getElementById("quantite");
 quant.value=tabQuant[document.getElementById("unites").selectedIndex];
}



function masquer(){

    document.getElementById("boutonunite").style.visibility='hidden';
    document.getElementById("allunites").style.visibility='hidden';
    document.getElementById("quantite").style.visibility='hidden';
    document.getElementById("okquantite").style.visibility='hidden';
}


function afficher(){
    document.getElementById("boutonunite").style.visibility='visible';
    document.getElementById("allunites").style.visibility='visible';
    document.getElementById("quantite").style.visibility='visible';
    document.getElementById("okquantite").style.visibility='visible';
}

masquer();












function actualiserImageFormRecette(idRecette){
    
    str=recupererImageRecette(idRecette);
    //console.log("str ",str);
    
    if (str==="") {
        $('#imgRecette').attr('src', "");
        //alert("ici");
    }else{
        //alert("onchange"+str);
        $('#imgRecette').attr('src', str);
    }
}





function recupererImageRecette(idRecette){
    //unit=document.getElementById("unites");
    jsonData={};
    jsonData['service']= 'Recette';
    jsonData['method']= 'getImageRecette';
    jsonData['id_recette']= idRecette;


    console.log(jsonData);

    var res="";
    $.ajax({
        type: 'POST',
        data: jsonData,
        url: urlWebService,
        dataType: 'json',
        async:false,
        success: function(data) {
            console.log("ici",data['response']);
            if(data['response']===false){
                res= "";
            }else{
                res= data['response'];
            }
        }
    });
    console.log("res",res);
    return res;
}




function changerImage(idInput) {
    input=document.getElementById(idInput);
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
        //alert("onload"+e.target.result);
        $('#imgRecette').attr('src',e.target.result);
        return true;
    };
    reader.readAsDataURL(input.files[0]);

  }
}



$(document).ready(function(){

    $("#inputFile").change(function(){
        //alert("on change hors cond");
        if (document.getElementById('inputFile').value!=='') {
            //alert("on change dans cond");
            changerImage('inputFile') ;
        }
    });


    $("#btnAjouterCategorie").click(function(){
        ajouterCategorieBase($("#categorieValue").val());
    });


    $("#btnAjouterIngredient").click(function(){
        ajouterIngredientBase($("#ingredientValue").val());
    });


});





function ajouterCategorieBase(valueCat){

    
    jsonData={
        service : 'Categorie',
        method : 'insertCategorie',
        value : valueCat
    }

    console.log(jsonData);

    $.ajax({
        type: 'POST',
        data: jsonData,
        url: urlWebService,
        dataType: 'json',
        success: function(data) {
            console.log(data);
            val = data.response;     //test à faire : si >0 ==> insertion faite
            if(val>0){
                label=$('#categorieValue').val();
                $('#id_cat').append('<option value=\"'+val+'\" selected>'+label+'</option>');
                $('#btnCancelCategorie').click();
            }else{
                alert(data.apiErrorMessage);
            }
        }
    }); 
}


function ajouterIngredientBase(valueIng){

    
    jsonData={
        service : 'Ingredient',
        method : 'insertIngredients',
        value : valueIng
    }

    console.log(jsonData);

    $.ajax({
        type: 'POST',
        data: jsonData,
        url: urlWebService,
        dataType: 'json',
        success: function(data) {
            console.log(data);
            val = data.response;     //test à faire : si >0 ==> insertion faite
            if(val>0){
                label=$('#ingredientValue').val();
                $('#ingredients').append('<option value=\"'+val+'\" selected>'+label+'</option>');
                $('#unites').append('<option value=\"'+val+'\" selected>...</option>');

                //tab
                tabUnit.push('rien');
                tabQuant.push(1);

                $('#btnCancelIngredient').click();
            }else{
                alert(data.apiErrorMessage);
            }
        }
    }); 
}