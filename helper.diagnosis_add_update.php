<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once ABS_PATH_TO_EHR."funDiagnosisHelper.php";

//! Condition for get diagnosis details by admission in case of encounter service...
if(ENABLE_IPD_MANAGEMENT == TRUE && IPD_PATIENT_ENCOUNTER_SERVICE_ID==$iServiceID){
    //! Get admission final dignosis data...
    $aDiagnosisData = getAdmissionFinalDiagnosis($iAdmitID);
    
    //! Get admission provisional dignosis data...
    $aProvisionalDiagnosis = getAdmissionProvisionalDiagnosis($iAdmitID);
}
else{
    if($iConsultationID>0) {
        $aDiagnosisData = fGetDiagnosisForConsultationID($iConsultationID);
        $aProvisionalDiagnosis = fGetProvisionalDiagnosisForConsultationID($iConsultationID);
    }
    else {
        $aDiagnosisData = array();
        $aProvisionalDiagnosis = array();
    }
}


$aDiagnosisCode = array();
$aDiagnosis = array();
$sDiagnosisCode = '';
$sDiagnosis = '';

if(!empty($aDiagnosisData)){
    
    for ($iii=0; $iii < count($aDiagnosisData); $iii++) { 
        $aDiagnosisCode[] = $aDiagnosisData[$iii]['icd10'];
        $aDiagnosis[] = $aDiagnosisData[$iii]['diagnosis'];
    }
}

if(!empty($aDiagnosisCode) && !empty($aDiagnosis)){
    
    $sDiagnosisCode = implode(",",$aDiagnosisCode);
    $sDiagnosis = implode(":",$aDiagnosis);
}

$sProvisionalDiagnosis = '';
if(!empty($aProvisionalDiagnosis)){

    $sProvisionalDiagnosis = stripslashes($aProvisionalDiagnosis['diagnosis']);
}
?>

<script type="text/javascript">
    
    $(document).ready(function(){
        
        var updateMode = $("#idUpdateMode").length;
        //! Hide the Edit Diagnosis buton in update mode after we save and finalize the consultation.
        if(updateMode == 1){
            $("#idEditDiagnosis").hide();
        }

        $("#container_for_diagnosis").dialog({
            bgiframe: true,
            autoOpen: false,
            width:550,
            draggable: false,
            position: "center",
            height: 500,
            resizable: false,
            title: 'MediXcel ICD-10 Diagnosis Engine',
            modal: true
        });

        //! Make a list of selected diagnosis list to edit it
        $("#idEditDiagnosis").click(function(){

            var sDiagnosis     = $("#TaFinalDiagnosis").val();
            var sDiagnosisCode = $("#idTextFinalCodeNo").val();
            
            if(sDiagnosis != '' && sDiagnosisCode != ''){

                var aDiagnosis = sDiagnosis.split(":");
                var aDiagnosisCode = sDiagnosisCode.split(",");
                $("#idEditDiagnosisOptions").empty();
                var sEditOptions = "<ul>";
                for(var kkk=0;kkk<aDiagnosisCode.length;kkk++){

                    sEditOptions += "<li class='classSearchListing' id='idLiIMG_"+kkk+"'><span class='classListingImg'><img id='idIMG_"+kkk+"' src='images/delete.gif' onclick=fRemoveDiagnosis('"+kkk+"','"+aDiagnosisCode[kkk]+"'); /></span>"+aDiagnosis[kkk]+" ("+aDiagnosisCode[kkk]+")</li>"; 
                }

                sEditOptions += "</ul>";
                
                $("#idEditDiagnosisOptions").html(sEditOptions);
            }else{
                $("#idEditDiagnosisOptions").html('No Data Found');
            }
        });
    });


    function createObject() {
    	var request_type;
    	var browser = navigator.appName;
    	if(browser == "Microsoft Internet Explorer"){
    	request_type = new ActiveXObject("Microsoft.XMLHTTP");
    	}else{
    		request_type = new XMLHttpRequest();
    	}
    		return request_type;
    }

    var httpn = createObject();

    /* -------------------------- */
    /* SEARCH					 */
    /* -------------------------- */
    function autosuggest() {
        q = document.getElementById('rtUncodedKeywordToSearch').value;
        // Set te random number to add to URL request
        nocache = Math.random();
        httpn.open('get', 'rtICD10Framework.php?searchParam='+q+'&nocache = '+nocache);

        httpn.onreadystatechange = autosuggestReply;
        httpn.send(null);
    }

    function autosuggestReply() {
        if(httpn.readyState == 4){
        	var response = httpn.responseText;
        	e = document.getElementById('rtDisplayICDResultsWidget');
        	if(response!=""){
        		e.innerHTML=response;
        		e.style.display="block";
        	} else {
        		e.style.display="none";
        	}
        }
    }


    function rtClickToAddICDCodes(iSearchCounter){

        oCodeControl = 'search_result_code_'+iSearchCounter;
        oDescControl = 'search_result_desc_'+iSearchCounter;
        oJQCodeControl = '#search_result_code_'+iSearchCounter;
        oJQDescControl = '#search_result_desc_'+iSearchCounter;

        newValCode = $(oJQCodeControl).text();
        newValDesc = $(oJQDescControl).text();
        
        alert ("You are adding ICD Code  "+newValDesc);

        
        oldValCode = document.getElementById('idTextFinalCodeNo').value;
        oldValDesc = document.getElementById('TaFinalDiagnosis').value;
        
        if (oldValCode != '')
        {
    	document.getElementById('idTextFinalCodeNo').value = oldValCode + ',' + newValCode;
    	document.getElementById('TaFinalDiagnosis').value = oldValDesc + ':' + newValDesc;
        }else
        {
    	document.getElementById('idTextFinalCodeNo').value = newValCode;
    	document.getElementById('TaFinalDiagnosis').value = newValDesc;
        }

        //document.getElementById('rtDisplayICDResultsWidget').innerHTML = '';
        //document.getElementById('rtUncodedKeywordToSearch').value = '';
    }

    function rtClickToAddICDCodesAbnormal(iSearchCounter){

        oCodeControl = 'search_result_code_'+iSearchCounter;
        oDescControl = 'search_result_desc_'+iSearchCounter;
        oJQCodeControl = '#search_result_code_'+iSearchCounter;
        oJQDescControl = '#search_result_desc_'+iSearchCounter;

        newValCode = $(oJQCodeControl).val();
        newValDesc = $(oJQDescControl).val();

        alert ("You are adding ICD Code  "+newValDesc);

        
        oldValCode = document.getElementById('idTextFinalCodeNo').value;
        oldValDesc = document.getElementById('TaFinalDiagnosis').value;

        if (oldValCode != '')
        {
    	document.getElementById('idTextFinalCodeNo').value = oldValCode + ',' + newValCode;
    	document.getElementById('TaFinalDiagnosis').value = oldValDesc + ':' + newValDesc;
        }else
        {
    	document.getElementById('idTextFinalCodeNo').value = newValCode;
    	document.getElementById('TaFinalDiagnosis').value = newValDesc;
        }

        document.getElementById('rtDisplayICDResultsWidget').innerHTML = '';
        document.getElementById('rtUncodedKeywordToSearch').value = '';
    }

    function rtFwICD10CodeSearch(idOfInputForKeyword){
        
        var sKeywordToSearch = document.getElementById(idOfInputForKeyword).value;

        if (sKeywordToSearch == '')
        {
            alert ("You must enter a Keyword to search");
            return false;
        }

        if(sKeywordToSearch.length < 3){
            alert ("Enter atleast 3 characters to search");
            return false;
        }

        autosuggest();
        return true;
    }

    function function_modal_rt(script){
        
       document.getElementById("container_for_diagnosis").innerHTML = "";
        $("#container_for_diagnosis").load(script);
        $("#container_for_diagnosis").dialog('open');
    }

    function rtFwICD10Code(idToInfluenceDesc,idToInfluenceCode){
        
        rtPresentContextForEncoding = idToInfluenceDesc;
        rtPresentContextForDecoding = idToInfluenceCode;
        var dataExisting = document.getElementById(idToInfluenceDesc).value;

        function_modal_rt('rtICD10Display.php');
    }

   
    //! brief function to remove the selected diagnosis
    function fRemoveDiagnosis(iRow,sDiagnosisCode){

        if (confirm("Do you really want to remove the '"+sDiagnosisCode+"' diagnosis code?")) {

            var sDiagnosisTemp     = $("#TaFinalDiagnosis").val();
            var sDiagnosisCodeTemp = $("#idTextFinalCodeNo").val();
            
            if(sDiagnosisTemp != '' && sDiagnosisCodeTemp != ''){

                var aDiagnosis = sDiagnosisTemp.split(":");
                var aDiagnosisCode = sDiagnosisCodeTemp.split(",");
                var aFinalD = [];
                var aFinalDCode = [];

                for(var kkk=0;kkk<aDiagnosisCode.length;kkk++){

                    if(aDiagnosisCode[kkk] != sDiagnosisCode){
                        aFinalD.push(aDiagnosis[kkk]);
                        aFinalDCode.push(aDiagnosisCode[kkk]);
                    }
                }

                aFinalD     = aFinalD.join(':');
                aFinalDCode = aFinalDCode.join(',');
                
                $("#TaFinalDiagnosis").val(aFinalD);
                $("#idTextFinalCodeNo").val(aFinalDCode);
                $("#idLiIMG_"+iRow).remove();
            }
        }
    }
</script>
<style type="text/css">
.classListingImg{margin-right: 10px;}
.classModalWidgetSearchBody{height: 500px;}
.classDiagnosisHR{margin: 0px;}
</style>
<div class='classDivStyle'  id='containerDiv' >
    <div class="classSubPanelHeading" id='idSubPanelHeading'>
        <img src="images/11.png" class="classExpandConsultationScreenHelperContentHeaderMainContainer" align="right" title="Maximize"/>
        <h7 class="classSubPanelTitle" id='idSubPanelTitle'>Diagnosis</h7>
    </div>

    <?php 
    if($aServiceSubservicePermissionSet[20] == "read_write" || $aServiceSubservicePermissionSet[20] == "write"){
        ?>
        <div class="classDivWidget classExpandConsultationScreenHelperContentMainContainer">
            <input type="hidden" name="isDiagnosisHelper" id="isDiagnosisHelper" value="400" />
            <span class="classLabel">Provisional Diagnosis</span>
            <br />
            <textarea name='taProvisionalDiagnosis' id='TaProvisionalDiagnosis' class='classInputTextarea span10' cols='80'><?php echo $sProvisionalDiagnosis;?></textarea>       
            <br /><br />
            <table class="classTableAligned">
                <tr>
                    <td>
                        <span class="classLabel">Final Diagnosis</span>  
                        <!--<a title='Click to Add' onclick="rtFwICD10Code('TaFinalDiagnosis','idTextFinalCodeNo');" >
                        <img src="images/rt_icon_add.png" width=42 height=42 />
                        </a>-->
                        <!-- <input type="button" value="Load Engine" class="btn btn-inverse" title="Click to Add" onclick="rtFwICD10Code('TaFinalDiagnosis','idTextFinalCodeNo');"/> -->
                        <input type="button" value="Load Engine" class="btn btn-inverse" title="Click to Add" id='idLoadEngine' data-toggle="modal" data-target="#rtDisplayICDSearchWidget" />
                        <textarea name='taFinalDiagnosis' id='TaFinalDiagnosis' tabindex='47' class='span3' cols='50' readonly='readonly'><?php echo $sDiagnosis; ?></textarea>
                    </td>
                    <td>
                        <span class="classLabel">Code No.</span>
                        <textarea name='textFinalCodeNo' id='idTextFinalCodeNo' tabindex='48' class='span3' cols='20' readonly='readonly'><?php echo $sDiagnosisCode;?></textarea>
                    </td>
                    <td>
                        <input type='button' name='editDiagnosis' id='idEditDiagnosis' value='Edit Diagnosis' class='btn btn-primary' data-toggle="modal" data-target="#idEditDiagnosisModal" />
                    </td>
               </tr>
            </table>
            <div id="container_for_diagnosis" style='display:none;'></div>
        </div>
        <?php
    }else if($aServiceSubservicePermissionSet[20] == "read"){
        ?>
        <div class="classDivWidget">
            <input type="hidden" name="isDiagnosisHelper" id="isDiagnosisHelper" value="400" />
            <span class="classLabel">Provisional Diagnosis</span>
            <br />
            <textarea name='taProvisionalDiagnosis' id='TaProvisionalDiagnosis' class='classInputTextarea span10' cols='80' readonly ><?php echo $sProvisionalDiagnosis;?></textarea>       
            <br /><br />
            <table class="classTableAligned">
                <tr>
                    <td>
                        <span class="classLabel">Final Diagnosis</span>  
                        <!--<a title='Click to Add' onclick="rtFwICD10Code('TaFinalDiagnosis','idTextFinalCodeNo');" >
                        <img src="images/rt_icon_add.png" width=42 height=42 />
                        </a>-->
                        <textarea name='taFinalDiagnosis' id='TaFinalDiagnosis' tabindex='47' class='span3' cols='50' readonly='readonly'><?php echo $sDiagnosis; ?></textarea>
                    </td>
                   <td>
                       <span class="classLabel">Code No.</span>
                       <textarea name='textFinalCodeNo' id='idTextFinalCodeNo' tabindex='48' class='span3' cols='20' readonly='readonly'><?php echo $sDiagnosisCode;?></textarea>
                   </td>
               </tr>
            </table>
            <div id="container_for_diagnosis" style='display:none;'></div>
        </div>
        <?php
    }else if($aServiceSubservicePermissionSet[20] == "no_access"){

        ?>
        <div class="classDivWidget classHidden">
            <input type="hidden" name="isDiagnosisHelper" id="isDiagnosisHelper" value="400" />
            <span class="classLabel">Provisional Diagnosis</span>
            <br />
            <textarea name='taProvisionalDiagnosis' id='TaProvisionalDiagnosis' class='classInputTextarea span10' cols='80' readonly ><?php echo $sProvisionalDiagnosis;?></textarea>       
            <br /><br />
            <table class="classTableAligned">
                <tr>
                    <td>
                        <span class="classLabel">Final Diagnosis</span>  
                        <!--<a title='Click to Add' onclick="rtFwICD10Code('TaFinalDiagnosis','idTextFinalCodeNo');" >
                        <img src="images/rt_icon_add.png" width=42 height=42 />
                        </a>-->
                        <textarea name='taFinalDiagnosis' id='TaFinalDiagnosis' tabindex='47' class='span3' cols='50' readonly='readonly'><?php echo $sDiagnosis; ?></textarea>
                    </td>
                   <td>
                       <span class="classLabel">Code No.</span>
                       <textarea name='textFinalCodeNo' id='idTextFinalCodeNo' tabindex='48' class='span3' cols='20' readonly='readonly'><?php echo $sDiagnosisCode;?></textarea>
                   </td>
               </tr>
            </table>
            <div id="container_for_diagnosis" style='display:none;'></div>
        </div>
        <?php
        //! Call the function to generate a message for permission no access for Diagnosis
        $sMessage = getMessageForNoAccess($test_name." Consultation","Diagnosis");
        echo $sMessage;

    //! Any one try to Hack the System. If is not in the SESSION
    }else{

        //! call the function to get the hacker details.
        $sHackerDetails = setHackerDetails($sIP);
    }
    ?>
</div>

<div class="modal hide" id="idEditDiagnosisModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="idEditDiagnosisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class = "modal-header">
                <h4 class="modal-title" id="idEditDiagnosisModalLabel">Edit Diagnosis List</h4>
            </div>
            <div class="modal-body">
                <div id='idEditDiagnosisOptions'></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="rtDisplayICDSearchWidget" class="modal hide classModal classModalWidget classModalWidgetSearch" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="idModalWidgetSearch" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class = "modal-header">
                <h4 class="modal-title" id="idModalWidgetSearch">ICD-10 Coding Engine <h5 style="color:#0a014d;">(Please Search specific word for better results)</h5></h4>
            </div>
            <div class="modal-body classModalWidgetSearchBody">
                <div class='classDivWidget'>
                    <span class="classMessage">Enter keyword and click on the blue button to search</span>
                    <br />
                    <input type="text" id="rtUncodedKeywordToSearch" class="classInputTextXXL classTextBox" value="" size="50"/> 
                    <a title='Click to Add' onclick="rtFwICD10CodeSearch('rtUncodedKeywordToSearch');" >
                        <img src="images/rt_search.png" width='30' height='30' />
                    </a>
                </div>
                <div id="rtDisplayICDResultsWidget" class="classModal classModalWidget classModalWidgetResults"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Adding script to move modal outside
    $(document).ready(function () {
        $('#rtDisplayICDSearchWidget').appendTo('body');
        $('#idEditDiagnosisModal').appendTo('body');
    });
</script>