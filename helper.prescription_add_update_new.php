<?php
include_once ABS_PATH_TO_EHR."funDrugs.php";
include_once ABS_PATH_TO_EHR."funPrescriptions.php";

if(ENABLE_EMERGENCY_IN_PRESCRIPTION){
    $sEmergencyVisibiity="display:block;";
}else{
    $sEmergencyVisibiity = "display:none;";    
}

$iIsIPDSchedule = ((new ScheduleManager($iScheduleID))->iIsIPDSchedule);

if ($iIsIPDSchedule == 1) {
    $aAdmissionScheduleDetails = getAdmissionIDByScheduleID($iScheduleID);

    if (!empty($aAdmissionScheduleDetails)) {
        $iAdmitID = $aAdmissionScheduleDetails['admit_id'];

        if ($iAdmitID > 0) {
            $iClinicID = fGetClinicIDByAdmitID($iAdmitID);
        }
    }
}

?>
<style type="text/css">
input.span2{width: 115px;}
.classInputTextXXS{width: 75px;}
.classHRSize{
    border: 1px solid #999;
}
.classEmergencyLabel, #idIsEmergency {
    display:inline-block;
}
.classPrescriptionRefillColorContainer{
    float: left;
    width: 25px;
    height: 25px;
    margin: 5px;
    border: 1px solid rgba(0, 0, 0, .2);
    cursor:pointer;
    margin-top: 0px;
}
</style>
<link rel="stylesheet" href="css/pnotify.custom.css">
<script src='js/pnotify.custom.js'></script>
<div class='classDivStyle classPrescriptionHelper'  id='containerDiv' >
    <div class="classSubPanelHeading" id='idSubPanelHeading'>
        <img src="images/11.png" class="classExpandConsultationScreenHelperContentHeaderMainContainer" align="right" title="Maximize"/>
        <h7 class="classSubPanelTitle" id='idSubPanelTitle'>Prescription</h7>
        <a class="classButtonShowPrescriptionInConsultationHistory pull-right btn btn-small btn-info" id="idButtonShowPrescriptionInConsultationHistory" data-consultation-id="<?php echo isset($iConsultationID) ? $iConsultationID : 0; ?>" title="<?php echo IN_CONSULTATION_HISTORY_LABEL;?>" style="margin-top: 5px; margin-left: 5px;"><?php echo IN_CONSULTATION_HISTORY_LABEL;?></a>
        <div class="pull-right classDivSetAsEmergencyBlock" style="<?php echo $sEmergencyVisibiity;?>padding: 1px;">
            <label class="classEmergencyLabel" for="idIsEmergency" style="color:#FFFFFF !important;">Set as Emergency</label>&nbsp;
            <input type="checkbox" id="idIsEmergency" name="idIsEmergency" data-toggle="toggle" data-on="Yes" data-off="No">
        </div>
    </div>
    <div id="idHasCurrentMedicationNote" class="alert alert-block" style="display: none;">
        <p>This patient has on-going mediciations. Please verify before prescribing new medicines.</p> 
        <a href="#idCurrentMedication" role="button" class="btn btn-success" data-toggle="modal">View Current Medications</a>
    </div>
    <?php 
    if($aServiceSubservicePermissionSet[21] == "read_write" || $aServiceSubservicePermissionSet[21] == "write"){
        ?>
        <input type='hidden' name='isPrescriptionHelper' value='9746' />
        <input type='hidden' name='iPrescriptionID' id ="iPrescriptionID" value='<?php echo $iPrescriptionID;?>' />
        <div class='classDivStyle classExpandConsultationScreenHelperContentMainContainer' id='containerData'>

            <?php
            if(PRESCRIPTION_TEMPLATE && PermissionHandler::canPerformAction($oSessionManager, 303)){
                ?>
                <div class='classDivStyle'>
                    <div class="row-fluid" >
                        <small><strong>Prescription Templates</strong></small>
                        <select type='text' class='classSelectBox classPrescriptionTemplate' name='idPrescriptionTemplateNew' id='idPrescriptionTemplateNew'></select>
                        <input type="hidden" name="idPrescriptionId" id="idPrescriptionId">
                        <button type="button" id="idBtnAddPrescription" class="btn btn-primary classBtnAddPrescription">Add</button>
                    </div>
                </div>
                <br>
                <?php
            }
            ?>
            <!-- Headings -->
            <div class='classDivStyle' >
                <div id='idPrescriptionContainer'></div>
            </div>
            <a  class='btn btn-small btn-warning' id ="idAddPrescription">Add Prescription</a>
            <?php
                if(ENABLE_PRESCRIPTION_REFILL){
                    ?>
                    <input type="button" name="idRefillPrescription" class="btn btn-info btn-small" id="idRefillPrescription" value="Refill Prescription" title="Refill Prescription"/>
                    <?php
                }
            ?>
            <?php
                if(ENABLE_PRESCRIPTION_ALERT){
                    ?>
                    <input type="button" class="btn btn-info btn-small" id="idCheckDrugInteractions" value="Check Drug Interactions" title="Check Drug Interactions"/>
                    <input type="button" class="btn btn-info btn-small" id="idContractIndications" value="Check Contra-Indications" title="Check Contra-Indications"/>
                    <?php
                }
            ?>
            <br/><br/>
         </div>
    <?php
    }else if($aServiceSubservicePermissionSet[21] == "read"){

        echo "You have only read permission to add Prescription helper";

    }else if($aServiceSubservicePermissionSet[21] == "no_access"){

        //! Call the function to generate a message for permission no access for History of patient
        $sMessage = getMessageForNoAccess($test_name." Consultation","Prescription");
        echo $sMessage;

        //! Any one try to Hack the System. If is not in the SESSION
    }else{

        //! call the function to get the hacker details.
        $sHackerDetails = setHackerDetails($sIP);
    }
    ?>
</div>
<!-- modal for current medicaltion-->
<div class="modal hide fade modal-lg" id="idCurrentMedication" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="idPrintReportLabel">Current Medication</h4>
            </div>
            <div class="modal-body">
                <table class='table table-condensed classTableView' id='idTableCurrentMedication'>
                    <thead>
                        <tr class='classTableHeader' id='idTableHeader'>
                            <th>Drug Name</th>
                            <th>Formulation</th>
                            <th>Strength</th>
                            <th>Frequency</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody id="idTableCurrentMedicationBody"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="idClose" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- Modal for refilling patient prescriptions -->
<div id="idModalRefillPatientPrescription" class="modal fade hide" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="idModalRefillPatientPrescriptionLabel" aria-hidden="true" style="width: 1100px;margin-left: -530px;">
    <div class="modal-header special-modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="idModalRefillPatientPrescriptionLabel">Refill <?php echo(PATIENT_OR_MEMBER) ?> Prescription</h3>
    </div>
    <div class="modal-body classModalLarge">
        <div class="row-fluid">
            <table class='table table-condensed table-hover classTableView' id='idTablePatientRefillDrugListing'>
                <thead class='classTableHeader' id='idTableHeader'>
                    <tr>
                        <th>Drug Code</th>
                        <th>Generic Name</th>
                        <th>Drug Name</th>
                        <th>Formulation</th>
                        <th>Strength</th>
                        <th>Frequency</th>
                        <th>Dose</th>
                        <th>Instructions</th>
                        <th>Date</th>
                        <th>Dispense Status</th>
                        <th><input type="checkbox" class="classSelectAllPatientRefillDrugs"/></th>
                    </tr>
                </thead>
                <tbody id="idTableBodyPatientRefillDrugListing"></tbody>
            </table>
        </div>
        <div class="row-fluid"><strong><span class="classPrescriptionRefillColorContainer" style="background-color:#ffe5a2;"></span><span>Indicates the drug is in Patient's Current Medication.</span></strong></div>
    </div>
    <div class="modal-footer classModalFooter">
        <span class="pull-left"><input type="checkbox" name="idShowMineRefillOnly" id="idShowMineRefillOnly">&nbsp;&nbsp;Drug(s) prescribed by me only.</span>
        <?php
            if(ENABLE_PRESCRIPTION_REFILL == true){
                ?>
                <button type="button" class="btn btn-success classBtnRefillPatientPrescription" title="Refill Drugs" aria-hidden="true">Refill Drugs</button>
                <?php
            }
        ?>
        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true" title="Close Modal">Close</button>
    </div>
</div>

<!-- Modal for in consultation prescription history -->
<div id="idModalInConsultationPrescriptionHistory" class="modal fade hide" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ididModalInConsultationPrescriptionHistoryLabel" aria-hidden="true" style="width: 1100px;margin-left: -530px;">
    <div class="modal-header special-modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="ididModalInConsultationPrescriptionHistoryLabel">In-Consultation History For Prescription</h3>
    </div>
    <div class="modal-body classModalLarge">
        <div class="row-fluid">
            <div class="alert alert-info" style="margin: 10px;"><p style="text-align:center;">No In-Consultation History Available for this Prescription</p></div>
        </div>
    </div>
    <div class="modal-footer classModalFooter">
        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true" title="Close Modal">Close</button>
    </div>
</div>
<!-- Modal for reason for stop prescription . -->
<div id="idModalReasonForStopPrescriptionsNew" class="modal hide" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="idModalReasonForStoprescriptionsLabelNew" aria-hidden="true" style="width: 1000px;margin-left: -530px;">
    <div class="modal-header special-modal-header">
        <button type="button" class="close" id="idCloseCancelReasonCrossBtn" aria-hidden="true">&times;</button>
        <h3 id="idModalReasonForStopPrescriptionsLabelNew">Reason For Stop Prescription <span class="classShowPatientNameForPrescription"></span></h3>
    </div>
        <input type="hidden" name="idUserID" id="idUserID" value="<?php echo $iUserID;?>">
        <input type="hidden" name="idPatientID" id="idPatientIDForPrescription">
        <input type="hidden" name="idAdmitIDForPrescription" id="idAdmitIDForPrescription">

        <div class="modal-body classModalLarge">
            <hr>
            <div class="classPrescriptionHistory row-fluid" style="margin-top: -45px;">
                <div class="row-fluid">
                    <div class="span1">
                        <label>Reason</label>
                    </div>
                    <div class="span8">
                         <textarea name='idStopPrescriptionReasonNew' id='idStopPrescriptionReasonNew' class='classStopPrescriptionReasonNew' style="width: 700px;height: 100px"></textarea>
                         <input type="hidden" name="idPrescriptionItemID" id="idPrescriptionItemID" >
                          <input type="hidden" name="iIsStopped" id="iIsStopped" >
                    </div>
                </div>

            </div>

        </div>
        <div class="modal-footer classModalFooter">
             <button type="button" class="btn btn-success"  aria-hidden="true" id="idCloseStopReasonBtn">Yes To Proceed</button>
               <button type="button" class="btn btn-danger"  aria-hidden="true" id="idCloseCancelReasonBtn">Cancel</button>
        </div>
</div>
 <!-- Popover -->
<div id="idPopoverPrescriptionInfo" class="hide">       
    <div class="custom-popover-header"><strong>Stop Prescription Details</strong></div>
    <div class="popover-content">
        <div class="row-fluid classContainerReasonInfo">
            <div class="span1">
                <i class="fa fa-file-text-o" aria-hidden="true" title="Reason"></i>                        
            </div>
            <div class="span11">
                <span id="idPopoverPrescriptionReason"></span>                        
            </div>
        </div>
        <div class="row-fluid classContainerUserInfo">
            <div class="span1">
                <i class="fa fa-user" aria-hidden="true" title="User"></i>                        
            </div>
            <div class="span11">
                <span id="idPopoverPrescriptionUser"></span>                        
            </div>
        </div>
        <div class="row-fluid classContainerTimeInfo">
            <div class="span1">
                <i class="fa fa-clock-o" aria-hidden="true" title="DateTime"></i>                        
            </div>
            <div class="span11">
                <span id="idPopoverPrescriptionTime"></span>                        
            </div>
        </div>                
    </div>
</div>
<div id="idPrescriptionAlertModal" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Prescription Drug Indications & Contra-indications</h3>
    </div>
    <div class="modal-body">
        <p>We have found following Drug Indications & Contra-indications:</p>
        <br/>
        <ul id="idPrescriptionDrugAlerts"></ul>
        <br/>
        <p>Are you sure want to continue?</p>
    </div>
    <div class="modal-footer">
        <a id="idPrescriptionAlertModalContinue" href="#" class="btn btn-primary">Continue</a>
        <a data-dismiss="modal" href="#" class="btn btn-warning">Close</a>
    </div>
</div>
<script type="text/javascript">
    var iConsultationId = <?php echo $iConsultationID; ?>;
    var iEnableSetAsEmergency = "<?php echo (ENABLE_EMERGENCY_IN_PRESCRIPTION == true) ? 1 : 0; ?>";
    var iEnableRefillPrescriptions = "<?php echo (ENABLE_PRESCRIPTION_REFILL == true) ? 1 : 0; ?>";
    var $aPatienActiveMedication = '<?php echo json_encode($aPatienActiveMedication); ?>';
    var aPatienActiveMedications = $.parseJSON($aPatienActiveMedication);
    var dDate = "<?php echo date("Y-m-d"); ?>";
    var iPatientID = "<?php echo isset($iPatientID) ? $iPatientID : 0; ?>";
    var iClinicID = "<?php echo isset($iClinicID) ? $iClinicID : 0; ?>";
    var dScheduleDate = "<?php echo $dScheduleDate; ?>";
    var iIsIPDPrescription = "<?php echo $iIsIPDSchedule; ?>";
    var iPrescriptionAlertLevel = "<?php echo PRESCRIPTION_ALERT_LEVEL ?>";
    var bEnablePrescriptionAlert = <?php echo ENABLE_PRESCRIPTION_ALERT ? "true":"false" ?>;
    var bPrescriptionIndicationsDisplayed = false;
    var oPrescriptionPad;
    var oPrescriptionDrugItem;
    var oRenderer;
    var oDrug;
    var iPrescriptionTemplateId=-1;
    var aRefilledDrugsContainer = [];

    const ALLOW_NEW_DRUG_IN_PRESCRIPTION_HELPER = <?php echo json_encode(ALLOW_NEW_DRUG_IN_PRESCRIPTION_HELPER); ?>;
    const CREATE_NEW_DRUG_ENTRY_IN_SYSTEM = <?php echo json_encode(CREATE_NEW_DRUG_ENTRY_IN_SYSTEM); ?>;

    $(document).ready(function(){
        
        //initialize PrescriptionPad object
        oPrescriptionPad = new PrescriptionPad();
        oPrescriptionPad.setAllowDrugsToBeAdded(ALLOW_NEW_DRUG_IN_PRESCRIPTION_HELPER);
        oPrescriptionPad.setAllowNewDrugsToBeCreated(CREATE_NEW_DRUG_ENTRY_IN_SYSTEM);
        
        // initialize renderer
        oRenderer = new PrescriptionPadRenderer();
        oRenderer.setAllowDrugsToBeAdded(ALLOW_NEW_DRUG_IN_PRESCRIPTION_HELPER);
        oRenderer.setAllowNewDrugsToBeCreated(CREATE_NEW_DRUG_ENTRY_IN_SYSTEM);

        // get all drug formulations
        getAllDrugFormulations();
        
        // get all drug frequencies
        getAllDrugFrequencies();

        //! get all roa..
        getAllRouteOfAdministration();

        //! Fetching current medications for patient..
        getPatientAllCurrentMedications(iPatientID, dDate, iClinicID);
        
        //create an empty prescription object 
        oPrescriptionDrugItem = new PrescriptionDrugItem();
        oPrescriptionPad.addPrescriptonDrugItem(0,oPrescriptionDrugItem);

        // get all previous prescription items for consultation Id
        if(iConsultationId > 0){
            getAllPrescriptionItems(iConsultationId);
        }else{
            oRenderer.createPrescriptionUI();
        }

        setTimeout(function() {
            $('.classDivSetAsEmergencyBlock .toggle').removeAttr("style");
            $('.classDivSetAsEmergencyBlock .btn').removeClass('btn-primary');
            $('.classDivSetAsEmergencyBlock .btn').addClass('btn-warning');
            $('.classDivSetAsEmergencyBlock .toggle-off').removeClass('btn-warning');
        }, 200);

        $(document).on("change",".classPrescriptionDrugName",function(){
            var iRow = $(this).attr('data-row');
            var iDrugSelected = $(this).val();
            var dStartDate = $('#idStartDate1').val();
            var dEndDate = $('#idEndDate1').val();
            let bIsNewDrug = false;

            if (ALLOW_NEW_DRUG_IN_PRESCRIPTION_HELPER === true) {
                if (isNaN(parseInt(iDrugSelected))) {
                    bIsNewDrug = true;
                }
            }

            oPrescriptionDrugItem = new PrescriptionDrugItem();
            if (bIsNewDrug) {
                oDrug = new Drug({drug_id:iDrugSelected,drug_name:iDrugSelected,generic_name:iDrugSelected,drug_code:'',formulation:'',strength:'',dosage:'',drug_for:'',instructions:'',drug_interaction:'',contra_indication:'',side_effects:'',category:'',manufacturer:'',product_cost:'',product_price:'',route_of_administration_id:'',route_of_administration:'',specialisation:''});
                oPrescriptionDrugItem.addDrug(oDrug,iRow);
                iDrugSelected = 0;
                oPrescriptionDrugItem.bIsNewDrug = true;
            } else {
                for(var iii=0; iii < aDrugsList.length; ++iii){
                    oDrug = new Drug(aDrugsList[iii]);
                    oPrescriptionDrugItem.addDrug(oDrug,iRow);
                }
            }

            oPrescriptionDrugItem.setSelectedDrug(iDrugSelected);

            var iDrugId = oPrescriptionDrugItem.getSelectedDrugId();
            oPrescriptionDrugItem.validatePrescriptionItem();
            oPrescriptionPad.addPrescriptonDrugItem(iRow-1,oPrescriptionDrugItem);
            oPrescriptionPad.setPrescriptionStartDate(iRow-1,dStartDate);
            oPrescriptionPad.setPrescriptionEndDate(iRow-1,dEndDate);
            oRenderer.clearFields(iRow);
            oRenderer.refreshPrescriptionView();
            getDrugAvailabilityByDrugId(iDrugId,iRow);
            getInteractionDrugsByDrugId(iDrugId,iRow, function(){
                if(bEnablePrescriptionAlert && iPrescriptionAlertLevel == 2){
                    displayDrugInteractionsAlert(iPrescriptionAlertLevel);
                    displayContractIndicationsAlert(iPrescriptionAlertLevel);
                }
            });
        });

        $(document).on("change",".classPrescriptionDate",function(){
            var iRow = $(this).attr('data-row');
            var iId = $(this).attr('id');
            var dDate = $(this).val();

            if(iId.indexOf("StartDate")>0){
                //start date
                oPrescriptionPad.setPrescriptionStartDate(iRow-1,dDate);
            }else{
                //end date
                oPrescriptionPad.setPrescriptionEndDate(iRow-1,dDate);
            }
            oRenderer.refreshPrescriptionView();
        });

        $(document).on("change",".classDose",function(){
            var iRow = $(this).attr('data-row');
            var iDose = $(this).val();

            oPrescriptionPad.setPrescriptionDose(iRow-1,iDose);
            oRenderer.refreshPrescriptionView();
        });

        $(document).on("change",".classPrescriptionDosage",function(){
            var iRow = $(this).attr('data-row');
            var sDosage = $(this).val();
            // extract dosage id 
            var aDosage = sDosage.split(",");

            oPrescriptionPad.setPrescriptionDrugDosage(iRow-1,aDosage[0]);
            oRenderer.refreshPrescriptionView();
        });

        $(document).on("change",".classDrugStrength",function(){
            var iRow = $(this).attr('data-row');
            var sStrength = $(this).val();
            var iDrugSelected = $(this).val();

            if (oPrescriptionPad.getPrescriptionDrugItem(iRow-1).bIsNewDrug === true) {
                iDrugSelected = 0;
                oPrescriptionPad.setStrength(iRow-1,sStrength);
            }

            oPrescriptionPad.setPrescriptionDrugStrength(iRow-1,iDrugSelected);
            oRenderer.refreshPrescriptionView();
        });

        $(document).on("change",".classNoOfTablets",function(){
            var iRow = $(this).attr('data-row');
            var iNoOfDays = $(this).val();
            oPrescriptionPad.setPrescriptionNoOfTablets(iRow-1,iNoOfDays);
        });

        $(document).on("change",".classInstructions",function(){
            var iRow = $(this).attr('data-row');
            var sIntsructions = $(this).val();
            oPrescriptionPad.setPrescriptionDrugInstructions(iRow-1,sIntsructions);
        });

        // set Formulation
        $(document).on("change",".classFormulation",function(){
            var iRow = $(this).attr('data-row');
            var sFormulation = $(this).val();
         
            oPrescriptionPad.setPrescriptionDrugFormulation(iRow-1,sFormulation);
            oRenderer.refreshPrescriptionView();
        });

        $(document).on('click','#idCheckDrugInteractions', function(e){
            e.preventDefault();

            displayDrugInteractionsAlert();
        });



        $(document).on('click','#idContractIndications', function(e){
            e.preventDefault();

            displayContractIndicationsAlert();
        });

        // set no of days manually
        $(document).on("change",".classDays",function(){
            var iRow = $(this).attr('data-row');
            var iDays = $(this).val();
         
            oRenderer.refreshPrescriptionView();
        });

        $(document).on("change",".classRouteOfAdministration",function(){
            var iRow = $(this).attr('data-row');
            var iROA = $(this).val();
            oPrescriptionPad.setPrescriptionDrugROA(iRow-1,iROA);
        });
        
        // to delete prescription
        $(document).on("click",".classDeletePrescription",function(){
            var iRow = $(this).attr('data-row');
            $(this).parent().parent().parent().remove();
            oPrescriptionPad.removePrescriptonDrugItem(iRow-1);
            oRenderer.refreshPrescriptionView();

            // if(oPrescriptionPad.getPrescriptionItemsCount()==1){
            //     $(this).parent().parent().parent().remove();
            //     oPrescriptionPad.removePrescriptonDrugItem(iRow-1);
            //     oRenderer.refreshPrescriptionView();
            // }else{
            //     $(this).parent().parent().parent().remove();
            //     oPrescriptionPad.removePrescriptonDrugItem(iRow-1);
            //     oRenderer.refreshPrescriptionView();
            // }
        });

        function getDrugInteractions(sInteraction){

            // History Helper
            // Presenting Complaints helper
            // Symptoms Helper (if itemmarked as Yes)
            // previous diagnosis from ICD-10 code helper
            // Nurse Notes Helper
            // Area of Concern Helper
            var messages = {};

            $('[data-prescription-alert-helper]').each(function(iIndex, currentHelper){
                var $helper = $(currentHelper),
                    sHelper = $helper.data('prescription-alert-helper');

                if(messages[sHelper] === undefined){
                    messages[sHelper] = [];
                }
                
                $helper.find("[data-prescription-alert-label]").each(function(iIndex, ele){
                    var $ele = $(ele),
                        sText = '';

                    if($ele.is('select')){
                        sText = $ele.find("option:selected").text();
                    } else if($ele.is('input')){
                        sText = $ele.val();
                    } else if($ele.is('textarea')){
                        var currentID = $ele.attr('id');
                        if(CKEDITOR.instances[currentID]){
                            sText = CKEDITOR.instances[currentID].getData();
                        } else {
                            sText = $ele.val();
                        }
                    } else {
                        sText = $ele.text();
                    }

                    if(sText && sText.toLowerCase().indexOf(sInteraction.toLowerCase()) > -1){
                        messages[sHelper].push({
                            'label': $ele.data('prescription-alert-label')
                        });
                    }
                });

            });

            if($('[data-prescription-alert-helper-symptoms]').length){
                var $helper = $('[data-prescription-alert-helper-symptoms]').first(),
                    sHelper = "Symptoms";

                    messages['Symptoms'] = [];
                    
                // classSymptomHelperLabel
                // classSymptomContainer
                // classSymptionSelectBox
                // classSymptionTextarea
                $helper.find(".classSymptomContainer").each(function(index, ele){
                    var $ele = $(ele),
                        $selectBox = $ele.find(".classSymptionSelectBox").first(),
                        $textArea = $ele.find(".classSymptionTextarea").first(),
                        $label = $ele.find(".classSymptomHelperLabel").first(),
                        isTruthyVal = $selectBox.length == 0 ? true : ($selectBox.val() && $selectBox.val() != "No");

                    if(isTruthyVal){
                        var currentLabel = $label.length ? $label.text() : "";

                        if($label.length && $label.text().toLowerCase().indexOf(sInteraction.toLowerCase()) > -1){
                            messages['Symptoms'].push(currentLabel);
                        } else if($textArea.length && $textArea.val().toLowerCase().indexOf(sInteraction.toLowerCase()) > -1) {
                            messages['Symptoms'].push(currentLabel);
                        }
                    }
                });
            }

            return messages;
        }

        function getDrugContractIndications(sContraIndication){
            return getDrugInteractions(sContraIndication);
        }

        window.getPrescriptionAlertsForConsultation = function(){
            var items = oPrescriptionPad.getAllFilledPrescriptionDrugItems(),
                aMessages = {
                    'drugInteractions': {
                        'drugs': [],
                        'nonDrugs': []
                    },
                    'contraIndications': []
                };

            if(items.length){
                $.each(items, function(iIndex,aPrescriptionDrugItem){
                    var aInteractions = aPrescriptionDrugItem.sInteractions.split(","),
                        sName = aPrescriptionDrugItem.sName;

                    $.each(aInteractions, function(iIndex, sInteraction){
                        if(sInteraction){
                            var oResult = getDrugInteractions(sInteraction),
                                aHelpers = [];
                                    
                            $.each(oResult, function(sHelper, aMessages){
                                if(aMessages.length > 0){
                                    aHelpers.push(sHelper);
                                }
                            });
    
                            if(aHelpers.length > 0){
                                aMessages.drugInteractions.nonDrugs.push("Possible Drug Interaction has been noted with the keyword <strong>"+sName+"</strong> in <strong>"+aHelpers.join(",")+" "+(aHelpers.length > 1 ? "Helpers":"Helper")+"</strong> for <strong>"+sName+"</strong>.");
                            }
                        }
                    });
                });

                // Now check for Drug Interaction
                $.each(items, function(iIndex,aPrescriptionDrugItem){
                    if(aPrescriptionDrugItem.aInteractionDrugs.length){
                        var aDrugIDs = $.map(aPrescriptionDrugItem.aInteractionDrugs, function(aInteractionDrug,iIndex){
                            return aInteractionDrug.interaction_drug_id;
                        });

                        var aDrugInteractions = $.map($.grep(items, function(aCurrentPrescriptionDrugItem,iIndex){
                            return aDrugIDs.indexOf(aCurrentPrescriptionDrugItem.iDrugId) > -1;
                        }), function(aCurrentPrescriptionDrugItem,iIndex){
                            return aCurrentPrescriptionDrugItem.sName;
                        });

                        if(aDrugInteractions.length){
                            aMessages.drugInteractions.drugs.push("<strong>"+aPrescriptionDrugItem.sName+"</strong> is currently prescribed on the same days and can have an Interaction with <strong>"+aDrugInteractions.join(",")+"</strong>.");
                        }
                    }
                });

                $.each(items, function(iIndex,aPrescriptionDrugItem){
                    var aContraIndications = aPrescriptionDrugItem.sContraIndications.split(","),
                        sName = aPrescriptionDrugItem.sName;

                    $.each(aContraIndications, function(iIndex, sInteraction){
                        if(sInteraction){
                            var oResult = getDrugContractIndications(sInteraction),
                                aHelpers = [];
                                    
                            $.each(oResult, function(sHelper, aMessages){
                                if(aMessages.length > 0){
                                    aHelpers.push(sHelper);
                                }
                            });
    
                            if(aHelpers.length > 0){
                                aMessages.contraIndications.push("<strong>"+sName+"</strong> is shown to be having contraindications with the keyword <strong>"+sInteraction+"</strong> in <strong>"+aHelpers.join(",")+" "+(aHelpers.length > 1 ? "Helpers":"Helper")+"</strong>.");
                            }
                        }
                    });
                });
            }

            return aMessages;
        }

        window.hasPrescriptionAlertsForConsultation = function(aMessages){
            return aMessages.drugInteractions.drugs.length > 0
            || aMessages.drugInteractions.nonDrugs.length > 0
            || aMessages.contraIndications.length > 0;
        }

        window.displayPrescriptionConsultationAlert = function(aMessage, callback){
            $("#idPrescriptionDrugAlerts").html("");
            $.each(aMessage.drugInteractions.drugs, function(index,sMessage){
                $("#idPrescriptionDrugAlerts").append("<li style='margin-left: 20px;list-style: disc;'>"+sMessage+"</li>");
            });

            $.each(aMessage.drugInteractions.nonDrugs, function(index,sMessage){
                $("#idPrescriptionDrugAlerts").append("<li style='margin-left: 20px;list-style: disc;'>"+sMessage+"</li>");
            });

            $.each(aMessage.contraIndications, function(index,sMessage){
                $("#idPrescriptionDrugAlerts").append("<li style='margin-left: 20px;list-style: disc;'>"+sMessage+"</li>");
            });

            $('#idPrescriptionAlertModal').modal('show');

            $( "#idPrescriptionAlertModalContinue").unbind("click");
            $( "#idPrescriptionAlertModalContinue").on("click", function(e){
                e.preventDefault();

                if(callback){
                    callback();
                }

                $('#idPrescriptionAlertModal').modal('hide');
            });
        }

        function displayDrugInteractionsAlert(iAlertLevel){
            var items = oPrescriptionPad.getAllFilledPrescriptionDrugItems(),
                bHasDrugInteraction = false;

            if(items.length){
                $.each(items, function(iIndex,aPrescriptionDrugItem){
                    var aInteractions = aPrescriptionDrugItem.sInteractions.split(","),
                        sName = aPrescriptionDrugItem.sName;

                    $.each(aInteractions, function(iIndex, sInteraction){
                        if(sInteraction){
                            var oResult = getDrugInteractions(sInteraction),
                                aHelpers = [];
                                    
                            $.each(oResult, function(sHelper, aMessages){
                                if(aMessages.length > 0){
                                    aHelpers.push(sHelper);
                                }
                            });

                            if(aHelpers.length > 0){
                                bHasDrugInteraction = true;
                                pNotifyPrescriptionAlert("Possible Drug Interaction has been noted with the keyword <strong>"+sInteraction+"</strong> in <strong>"+aHelpers.join(",")+" "+(aHelpers.length > 1 ? "Helpers":"Helper")+"</strong> for <strong>"+sName+"</strong>, please be advised.","error");
                            }
                        }
                    });
                });

                // Now check for Drug Interaction
                $.each(items, function(iIndex,aPrescriptionDrugItem){
                    if(aPrescriptionDrugItem.aInteractionDrugs.length){
                        var aDrugIDs = $.map(aPrescriptionDrugItem.aInteractionDrugs, function(aInteractionDrug,iIndex){
                            return aInteractionDrug.interaction_drug_id;
                        });

                        var aDrugInteractions = $.map($.grep(items, function(aCurrentPrescriptionDrugItem,iIndex){
                            return aDrugIDs.indexOf(aCurrentPrescriptionDrugItem.iDrugId) > -1;
                        }), function(aCurrentPrescriptionDrugItem,iIndex){
                            return aCurrentPrescriptionDrugItem.sName;
                        });

                        if(aDrugInteractions.length){
                            bHasDrugInteraction = true;
                            pNotifyPrescriptionAlert("<strong>"+aPrescriptionDrugItem.sName+"</strong> is currently prescribed on the same days and can have an Interaction with <strong>"+aDrugInteractions.join(",")+"</strong>, please be advised before you continue.","error");
                        }
                    }
                });
            }

            if(iAlertLevel != 2 && !bHasDrugInteraction){
                pNotifyPrescriptionAlert("No Drug possible Interaction found.","success");
            }
        }

        function displayContractIndicationsAlert(iAlertLevel){
            var items = oPrescriptionPad.getAllFilledPrescriptionDrugItems(),
                bHasDrugInteraction = false;

            if(items.length){
                $.each(oPrescriptionPad.getAllFilledPrescriptionDrugItems(), function(iIndex,aPrescriptionDrugItem){
                    var aContraIndications = aPrescriptionDrugItem.sContraIndications.split(","),
                        sName = aPrescriptionDrugItem.sName;

                    $.each(aContraIndications, function(iIndex, sInteraction){
                        if(sInteraction){
                            var oResult = getDrugContractIndications(sInteraction),
                                aHelpers = [];
                                    
                            $.each(oResult, function(sHelper, aMessages){
                                if(aMessages.length > 0){
                                    aHelpers.push(sHelper);
                                }
                            });

                            if(aHelpers.length > 0){
                                bHasDrugInteraction = true;
                                pNotifyPrescriptionAlert("<strong>"+sName+"</strong> is shown to be having contraindications with the keyword <strong>"+sInteraction+"</strong> in <strong>"+aHelpers.join(",")+" "+(aHelpers.length > 1 ? "Helpers":"Helper")+"</strong>, please be advised.","error");
                            }
                        }
                    });
                });
            }

            if(iAlertLevel != 2 && !bHasDrugInteraction){
                pNotifyPrescriptionAlert("No Drug possible Contra-Indication found.","success");
            }
        }

       /* //! Stop Prescription..
        $(".classPrescriptionHelper").on("click",".classStopResetPrescription",function(){
            var iRowID = $(this).data('row-id');
            //! Restart Prescription..
            if($(this).hasClass("btn-danger")){
                $(this).removeClass("btn-danger");
                $(this).addClass("btn-success");
                $(this).attr("title","Stop Prescription");
                $("#idPrescription"+iRowID).find("#idPrescriptionStatus"+iRowID).val('0');                
            }else{ //! Stop Prescription..
                $(this).removeClass("btn-success");
                $(this).addClass("btn-danger");
                $(this).attr("title","Restart Prescription");
                $("#idPrescription"+iRowID).find("#idPrescriptionStatus"+iRowID).val('1');                
            }
        });*/

        // when add prescription template is clicked
        $(document).on("change","#idPrescriptionTemplateNew",function(){
            iPrescriptionTemplateId = $(this).val();
        });

        $(document).on("click","#idAddPrescription",function(){   
            //create an empty prescription object 
            oPrescriptionDrugItem = new PrescriptionDrugItem();
            oPrescriptionDrugItem.validatePrescriptionItem();
            oPrescriptionPad.addPrescriptonDrugItem(oPrescriptionPad.aPrescriptionItems.length,oPrescriptionDrugItem);
            oRenderer.createPrescriptionUI();
        });

        // to add prescription templates
        $(document).on("click","#idBtnAddPrescription",function(){   
            //valid prescription template id 
            if(iPrescriptionTemplateId>-1){
                getPresciptionTemplateItemDetails(iPrescriptionTemplateId);    
            }
        });

        //! for show modal backdrop...
        $('.modal').on('shown.bs.modal', function() {
            //Make sure the modal and backdrop are siblings (changes the DOM)
            $(this).before($('.modal-backdrop'));
            //Make sure the z-index is higher than the backdrop
            $(this).css("z-index", parseInt($('.modal-backdrop').css('z-index')) + 1);
        });

        //! Refilling patient prescription..
        $(document).on('click', '#idRefillPrescription', function(){
            //! refilling patient drug details..
            fGetRefillingDrugDetailsForPatient(iPatientID,0,dScheduleDate,0);
            
            $("#idModalRefillPatientPrescription").modal('show');
        });

        //! for refilling drugs..
        $(document).on('click', '#idShowMineRefillOnly', function(){
            var iCreationUserID = 0;
            if($(this).prop("checked")){
                iCreationUserID = "<?php echo (new SessionManager())->iUserID; ?>";
            }
            
            //! refilling patient drug details..
            fGetRefillingDrugDetailsForPatient(iPatientID,iCreationUserID,dScheduleDate,0);
        });

        //! For check and uncheck all..
        $(document).on('click', '.classSelectAllPatientRefillDrugs', function(){
            if($(this).prop("checked")) {
                $(".classSelectPatientRefillDrugs").prop("checked", true);
            } else {
                $(".classSelectPatientRefillDrugs").prop("checked", false);
            }
        });

        //! refill drugs..
        $(document).on('click', '.classBtnRefillPatientPrescription', function(){
            var oChecked = {};
            oChecked.aRefilledDrugs = [];
            $(".classSelectPatientRefillDrugs:checkbox").each(function() {
                if ($(this).is(":checked")) {
                    oChecked.aRefilledDrugs.push($(this).data("drug-id"));
                }
            });
            var sRefilledDrugs = oChecked.aRefilledDrugs.toString();

            if(sRefilledDrugs == ''){
                pNotifyPrescriptionAlert("Please select atleast one drug to refill.", "error");
                return false;
            }
            var aRefillDrugID = [];
            $(".classSelectPatientRefillDrugs:checkbox").each(function() {
                if($(this).is(":checked")) {
                    var iDrugID = $(this).data('drug-id');
                    if(iDrugID > 0){
                        aRefillDrugID.push(iDrugID);
                    }
                }
            });

            //! Refill drugs..
            refillPrescriptionDrugs(aRefillDrugID);
        });

        // to refill drugs..
        $(document).on("click",".classBtnRefillPatientPrescription",function(){   
            if(iPrescriptionTemplateId>-1){
                getPresciptionTemplateItemDetails(iPrescriptionTemplateId);    
            }
        });

        // add prescription template
        $(".classPrescriptionHelper").find("#idPrescriptionTemplateNew").select2({                
            width: '100%',
            allowClear: false,
            minimumInputLength: 3,
            width: 'resolve',
            ajax: {
                url:"ajax_prescriptionTemplate.php?func=4",
                data: function (params) {
                    return {
                        sTemplateName: params.term,
                    };
                },
                processResults: function (data, params) {
                    var results=[];

                    for(var iii=0; iii< data.length; ++iii){
                        var iTemplateId   = data[iii].template_id;
                        var sTemplateName = data[iii].template_name;
                        var sTemplateDesc = data[iii].template_description;

                        results.push({
                            id: iTemplateId,
                            text: sTemplateName
                        });

                    }
                    return { results: results };
                }
            }
        });

        // function to get number of days based on frequency
        function fGetNumOfDaysByFrequencyetails(iFrequencyID,iNoOfDays){

            var sFrequencyCategory = $("#idFrequencyCategory_"+iFrequencyID).val();
            var iTimeBetweenDosage = $("#idFrequencyTimeBetwwenDosage_"+iFrequencyID).val();
            var iDosageCountInDay = $("#idDosageCountInDay_"+iFrequencyID).val();
            var iTotalDosageDays = iNoOfDays;

            switch(sFrequencyCategory) {

                case 'day':

                    if(iTimeBetweenDosage > 1){
                        iTotalDosageDays = iNoOfDays/iTimeBetweenDosage;
                        iTotalDosageDays = Math.round(iTotalDosageDays);
                    }

                break;

                case 'month':

                    iTotalDosageDays = iNoOfDays/30;
                    iTotalDosageDays = Math.round(iTotalDosageDays);
                    
                break;
            }

            return iTotalDosageDays;
        }

        //! For calculating no. of tablets
        $(".classPrescriptionHelper").on("change",".classCalculateNoOfTablets",function(){
            var iRowID = $(this).data('row');
            calculateNoOfTablets(iRowID);
        }); 

        //! Show in consultation history for prescription..
        $(document).on('click','.classButtonShowPrescriptionInConsultationHistory', function(e){
            var iConsultationID = $(this).data('consultation-id');
            $("#idModalInConsultationPrescriptionHistory").modal('show');

            //! Validation..
            if (iConsultationID > 0) {
                //! Fetching in consultation prescription history..
                getInConsultationPrescriptionHistory(iConsultationID);
            }
        });
   
    });// end of document ready

    function calculateNoOfTablets(iRowID)
    {
        var sFrequency = $("#idDosage"+iRowID).val();
        var sGenericName = $("#idGenericName"+iRowID).val();
        var iDrugID = $("#idDrugName"+iRowID).val();
        var dStartDate = $("#idStartDate"+iRowID).val();
        var dEndDate = $("#idEndDate"+iRowID).val();
        var sFormulation = $("#idFormulation"+iRowID).val();
        var iDose = $("#idDose"+iRowID).val();
        if(iDose && (typeof iDose == "string") && iDose.indexOf('.') > -1){
            var iDose = parseFloat(iDose);
        } else {
            var iDose = parseInt(iDose);
        }

        if(isNaN(iDose)){
            iDose = 0;
        }
        
        var sStrength = $("#idStrength"+iRowID+"  option:selected").text();
        var iStrength = parseInt(sStrength);
        var iNoOfTablets = 0;
        var iActualFrequency = 0;
        var aFormulations = ["Tablet","Capsule","Suppository","Roll","Piece","Package","KIT","Vial"];
        
        if($.inArray(sFormulation, aFormulations) !== -1){
            //! For calculating no. of tablets
            if(sFrequency != "" && dStartDate != "" && dEndDate != "" && $.isNumeric(iDose) == true && $.isNumeric(iStrength) == true){
                window.setTimeout(function() {
                    var iNoOfDays = $("#idNoOfDays"+iRowID).val();
                    var iActualFrequency = sFrequency.split(",")[1];
                    if(parseInt(iActualFrequency) > 0 && parseInt(iNoOfDays) > 0 && iDose > 0 && parseInt(iStrength) > 0){
                        iNoOfTablets = (iDose / parseInt(iStrength)) * parseInt(iActualFrequency) * parseInt(iNoOfDays);
                        $("#idNoOfTablets"+iRowID).val(parseInt(iNoOfTablets));
                        oPrescriptionPad.setPrescriptionNoOfTablets(iRowID-1,parseInt(iNoOfTablets));
                    }else{
                        $("#idNoOfTablets"+iRowID).val(0);    
                        oPrescriptionPad.setPrescriptionNoOfTablets(iRowID-1,0);
                    }
                }, 200);
            }else{
                $("#idNoOfTablets"+iRowID).val(0);
                oPrescriptionPad.setPrescriptionNoOfTablets(iRowID-1,0);
            }                
        }else{
            $("#idNoOfTablets"+iRowID).val(0);
            oPrescriptionPad.setPrescriptionNoOfTablets(iRowID-1,0);
        }

        var bRepeatPrescriptionFound = false;
        
        //! Checking for same prescription for patient
        if(iDrugID > 0 && dStartDate != '' && dEndDate != '' && sStrength != '' && aPrescriptionCurrentMedication.length > 0){
            dStartDate = moment(dStartDate,"DD-MM-YYYY").format('YYYY-MM-DD');
            dEndDate = moment(dEndDate,"DD-MM-YYYY").format('YYYY-MM-DD');
            $.each(aPrescriptionCurrentMedication, function(index, value){
                var iPrescribedDrugID = value.drug_id,
                    dPrescribedStartDate = value.start_date,
                    dPrescribedEndDate = value.end_date;

                dPrescribedStartDate = moment(dPrescribedStartDate,"DD-MM-YYYY").format('YYYY-MM-DD');
                dPrescribedEndDate = moment(dPrescribedEndDate,"DD-MM-YYYY").format('YYYY-MM-DD');

                if(iDrugID > 0){
                    if(iDrugID == iPrescribedDrugID){
                        if((dStartDate <= dPrescribedEndDate && dPrescribedStartDate <= dStartDate) || (dEndDate <= dPrescribedEndDate && dPrescribedStartDate <= dEndDate)){
                            bRepeatPrescriptionFound = true;
                        }
                    }
                }
            });
        }

        if(bRepeatPrescriptionFound == true){
            pNotifyPrescriptionAlert('The selected drug is already prescribed to this patient. For more details, please refer current medication(s) of the patient.', 'warning');
        }
    }
    
    $(document).on("click",".classStopResetPrescription",function(){
        var iPrescriptionItemID = $(this).data('row-id');
        var iIsStopped = 1;
        if($(this).hasClass('btn-danger')){
            iIsStopped = 0;
        }
        $('#iIsStopped').val(iIsStopped);
        if(iIsStopped==1){
            $("#idModalReasonForStopPrescriptionsNew").modal('show');
        }else{
            if($(this).hasClass("btn-danger")){
                $(this).removeClass("btn-danger");
                $(this).addClass("btn-success");
                $(this).attr("title","Stop Prescription");
                $("#idPrescriptionStatus"+iPrescriptionItemID).val('0');                
            }
        }
        $('#idPrescriptionItemID').val(iPrescriptionItemID);

    });
    $("#idCloseCancelReasonBtn").click(function(){
        var iPrescriptionItemID = $('#idPrescriptionItemID').val();
        $('#idStopPrescriptionReasonSave'+iPrescriptionItemID).val('');
        $('#idPrescriptionItemID').val('');
        $('#iIsStopped').val('0');
        $('#idStopPrescriptionReasonNew').val('');
        $("#idModalReasonForStopPrescriptionsNew").modal("hide");
    });
    $("#idCloseCancelReasonCrossBtn").click(function(){
        var iPrescriptionItemID = $('#idPrescriptionItemID').val();
    
        $('#idStopPrescriptionReasonSave'+iPrescriptionItemID).val('');
       /* if($('#idStopResetPrescription'+iPrescriptionItemID).hasClass("btn-danger")){
            $('#idStopResetPrescription'+iPrescriptionItemID).removeClass("btn-danger");
            $('#idStopResetPrescription'+iPrescriptionItemID).addClass("btn-success");
            $('#idStopResetPrescription'+iPrescriptionItemID).attr("title","Stop Prescription");
            $("#idPrescriptionStatus"+iPrescriptionItemID).val('0');    
            $('#idStopPrescriptionReasonNew').val('');            
        }*/
        $('#idPrescriptionItemID').val('');
        $('#iIsStopped').val('0');
        $('#idStopPrescriptionReasonNew').val('');
        $("#idModalReasonForStopPrescriptionsNew").modal("hide");
    });
    $("#idCloseStopReasonBtn").click(function(){
        var iPrescriptionItemID = $('#idPrescriptionItemID').val();
        var sStopPrescriptionReason = $('#idStopPrescriptionReasonNew').val();
        var iIsStopped =1;
         sStopPrescriptionReason=jQuery.trim(sStopPrescriptionReason);
            if(iIsStopped==1){
                if(sStopPrescriptionReason==''){
                    alert("Please enter the reason for stop prescription");
                    return false;
                }
                if($('#idStopResetPrescription'+iPrescriptionItemID).hasClass("btn-success")){
                    $('#idStopResetPrescription'+iPrescriptionItemID).removeClass("btn-success");
                    $('#idStopResetPrescription'+iPrescriptionItemID).addClass("btn-danger");
                    $('#idStopResetPrescription'+iPrescriptionItemID).attr("title","Restart Prescription");
                    $("#idPrescriptionStatus"+iPrescriptionItemID).val('1');                
                }
            }
        $('#idStopPrescriptionReasonSave'+iPrescriptionItemID).val(sStopPrescriptionReason);
        $('#idStopPrescriptionReasonNew').val('');
        $("#idModalReasonForStopPrescriptionsNew").modal("hide");
    });
    //! Popover..
    function popoverPatientInfo(){
        $(document).find('.classPopoverPrescriptionInfo').popover({
            trigger: "hover",
            container: 'body',
            html: true,
            content: function () {                            
                //! Reason..                         
                if($(this).attr('data-reason') == ''){
                    $($(this).data('popover-content')).find('.classContainerReasonInfo').css('display','none');
                }else{
                    $($(this).data('popover-content')).find('.classContainerReasonInfo').css('display','inline-block');
                }

                //! UserName..
                if($(this).attr('data-user') == '' || $(this).attr('data-user') == '-'){
                    $($(this).data('popover-content')).find('.classContainerUserInfo').css('display','none');
                }else{
                    $($(this).data('popover-content')).find('.classContainerUserInfo').css('display','inline-block');
                }

                //! added Time..
                if($(this).attr('data-datetime') == '' || $(this).attr('data-datetime') == '-'){
                    $($(this).data('popover-content')).find('.classContainerTimeInfo').css('display','none');
                }else{
                    $($(this).data('popover-content')).find('.classContainerTimeInfo').css('display','inline-block');
                }

                //! Append Data..
                var sReason = "&nbsp;: "+$(this).attr('data-stop-reason');
                console.log(sReason);
                var sUserName = "&nbsp;: "+$(this).attr('data-user');
                var sDateTime = "&nbsp;: "+$(this).attr('data-datetime');

                $($(this).data('popover-content')).find('#idPopoverPrescriptionReason').html(sReason).css({"word-break":"break-all"});
                $($(this).data('popover-content')).find('#idPopoverPrescriptionUser').html(sUserName);
                $($(this).data('popover-content')).find('#idPopoverPrescriptionTime').html(sDateTime);

                var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
                return clone;
            }
        }).click(function(e) {
            e.preventDefault();
        });
    }
    // function to get all drug formulations
    function getAllDrugFormulations(){
        $.ajax({
            url:"ajaxEhr.php?sFlag=getAllDrugFormulations",
            data : {},
            success: function(data){
                oPrescriptionPad.setPrescriptionDrugFormulations(data);
            } 
        });
    }// getAllDrugFormulations

    // function to get all drug formulations
    function getAllDrugFrequencies(){
        $.ajax({
            url:"ajaxEhr.php?sFlag=getAllDrugsFrequencies",
            data : {},
            success: function(data){
                oPrescriptionPad.setPrescriptionDrugDosageFrequencies(data);
            } 
        });
    }// getAllDrugFormulations

    // function to get drug availability
    function getDrugAvailabilityByDrugId(iDrugID,iRow){
        $.ajax({
            url: "ajaxEhr.php?sFlag=fGetDrugAvailabilityForDrugID",
            data: {iDrugID:iDrugID},
            success: function (data){
                oPrescriptionPad.setPrescriptionDrugAvailability(iRow-1,data);
                oRenderer.setPrescriptionAvailabilityStatus(iRow);
                oRenderer.refreshPrescriptionView();
            }
        });
    }   

    function getInteractionDrugsByDrugId(iDrugID,iRow, callback){
        $.ajax({
            url: "ajaxDrugs.php?sFlag=getInteractionDrugsByDrugId",
            data: {iDrugID:iDrugID},
            success: function (data){
                oPrescriptionPad.setInteractionDrugs(iRow-1,data);
                oRenderer.refreshPrescriptionView();
                if(callback){
                    callback();
                }
            }
        });
    }   

    // function to get all drug ROA
    function getAllRouteOfAdministration(){
        $.ajax({
            url:"ajaxEhr.php?sFlag=getAllRouteOfAdministration",
            success: function(data){
                oPrescriptionPad.setPrescriptionDrugROAs(data);
            } 
        });
    }// getAllDrugFormulations 
        

    // function to fetch previous prescription items for consultation id
    function getAllPrescriptionItems(iConsultationId){
        $.ajax({
            url:"ajaxEhr.php?sFlag=getAllPrescriptionItemsForConsultationId",
            data : {iConsultationId:iConsultationId,iIsIPDPrescription:iIsIPDPrescription},
            success: function(data){
                if ($.trim(data) !== false) {
                    // set prescription id
                    if(data.length > 0){
                        for(var iii =0; iii< data.length; ++iii){
                            var iRow = parseInt(iii)+1;
                            oPrescriptionDrugItem = new PrescriptionDrugItem();

                            var iDrugSelected =0;
                            oDrug = new Drug(data[iii]);
                            oPrescriptionDrugItem.addDrug(oDrug,0);
                           
                            oPrescriptionDrugItem.setSelectedDrug(iDrugSelected);
                            oPrescriptionDrugItem.setEndDate(data[iii]['end_date']);
                            oPrescriptionDrugItem.setStartDate(data[iii]['start_date']);
                            oPrescriptionDrugItem.setDrugDosage(data[iii]['dosage']);
                            oPrescriptionDrugItem.setFormulation(data[iii]['formulation']);
                            oPrescriptionDrugItem.setDose(data[iii]['prescription_dose']);
                            oPrescriptionDrugItem.setNoOfTablets(data[iii]['no_of_tablets']);
                            oPrescriptionDrugItem.setIsStopped(data[iii]['isStopped']);
                            oPrescriptionDrugItem.setDrugAvailability(data[iii]['drug_availability_status']);
                            oPrescriptionDrugItem.setInteractionDrugs(data[iii]['aInteractionDrugs']);
                            oPrescriptionDrugItem.setStoppingReason(data[iii]['reason_for_stopped'],data[iii]['sStoppedBy'],data[iii]['stopped_on']);

                            if ($.isEmptyObject(data[iii]['r_o_a']) == false) {
                                oPrescriptionDrugItem.setROA(data[iii]['r_o_a']['route_of_administration_id']);                                
                                oPrescriptionDrugItem.setROAName(data[iii]['r_o_a']['route_of_administration']);                                
                            }
                            oPrescriptionDrugItem.validatePrescriptionItem();     
                            oPrescriptionPad.addPrescriptonDrugItem(iRow-1,oPrescriptionDrugItem);
                            oRenderer.createPrescriptionUI();
                            oRenderer.refreshPrescriptionView();
                        }

                        // refresh the view
                        oRenderer.refreshPrescriptionView();
                        var iPrescriptionId = data[0]['prescription_id'];
                        $(".classPrescriptionHelper").find('#iPrescriptionID').val(iPrescriptionId);
                        if (iEnableSetAsEmergency == 1) {
                            var iIsEmergency = data[0]['set_as_emergency'];
                            if (iIsEmergency == 1) {
                                $(".classDivSetAsEmergencyBlock").find("#idIsEmergency").prop('checked', true).change();
                            } else {
                                $(".classDivSetAsEmergencyBlock").find("#idIsEmergency").prop('checked', false).change();
                            }
                        }
                        popoverPatientInfo();
                    } else {
                        oRenderer.createPrescriptionUI();
                    }                   
                }            
            }
        });
    }//getAllPrescriptionItems

    // to get prescription template prescriptions
    function getPresciptionTemplateItemDetails(iTemplateId){
        $.ajax({
            url:"ajax_prescriptionTemplate.php?func=1",
            type:"get",
            data:{iTemplateId:iTemplateId},
            success: function(data){
                var iExistingPrescription = oPrescriptionPad.getPrescriptionItemsCount();
                for(var iii=0 ; iii< data.length ; ++iii){
                    var iIndex = iExistingPrescription + parseInt(iii);
                    oPrescriptionDrugItem = new PrescriptionDrugItem();
                    var iDrugSelected =0;
                    oDrug = new Drug(data[iii]);
                    oPrescriptionDrugItem.addDrug(oDrug,0);
                    oPrescriptionDrugItem.setSelectedDrug(iDrugSelected);
                    oPrescriptionDrugItem.setNoOfDays(data[iii]['days'] ? data[iii]['days'] : 0);
                    oPrescriptionDrugItem.setStartDate(moment().format('DD-MM-YYYY'));
                    // oPrescriptionDrugItem.setEndDate("");
                    oPrescriptionDrugItem.setDrugDosage(data[iii]['dosage']);
                    oPrescriptionDrugItem.setFormulation(data[iii]['formulation']);
                    oPrescriptionDrugItem.setDose(data[iii]['prescription_dose']);
                    oPrescriptionDrugItem.setNoOfTablets(data[iii]['no_of_tablets']);
                    oPrescriptionDrugItem.setDrugInstructions(data[iii]['instruction']);
                    oPrescriptionDrugItem.validatePrescriptionItem();
                    oPrescriptionPad.addPrescriptonDrugItem(iIndex,oPrescriptionDrugItem);
                    oPrescriptionPad.setPrescriptionDrugAvailability(iIndex,data[iii]['drug_availability_status']);
                    oRenderer.setPrescriptionAvailabilityStatus(iIndex);
                    // oRenderer.createPrescriptionUI();
                    // oRenderer.refreshPrescriptionView();
                }

                // refresh the view
                oRenderer.refreshPrescriptionView();

                setTimeout(function(){
                    $('.classPrescriptionDosage').each(function(){
                        let id = $(this).data('row');
                        calculateNoOfTablets(id);
                    });
                },2000);
            }
        });

    }// get Prescription item details

    // function to get all drug ROA
    function getPatientAllCurrentMedications(iPatientID, dDate, iClinicID) {
        $.ajax({
            url:"ajaxEhr.php?sFlag=getPatientAllCurrentMedications",
            success: function(data){
                if (data.length > 0 && iEnableRefillPrescriptions == 1) {
                    oPrescriptionPad.setPatientCurrentMedications(data);
                    $("#idHasCurrentMedicationNote").show();
                }
            } 
        });
    }// getAllDrugFormulations

    //! Function for refilling patient drugs..
    function fGetRefillingDrugDetailsForPatient(iPatientID,iCreationUserID,dDate,iClinicID){
        $("#idTablePatientRefillDrugListing").find(".classSelectAllPatientRefillDrugs").prop('checked', false);
        aRefilledDrugsContainer = [];
        $.ajax({
            type: 'POST',
            url: "ajaxPharmacy.php?sFlag=fGetRefillingDrugDetailsForPatient",
            data: {iPatientID:iPatientID,iCreationUserID:iCreationUserID,dDate:dDate,iClinicID:iClinicID},
            success: function (data){
                if($.trim(data) != false){
                    if($.isEmptyObject(data) == false){
                        var sRefillTemplate = "";
                        aRefilledDrugsContainer = data;
                        $.each(data, function(iIndex, aRefillDrugs){
                            var sStyle = "";
                            if(aRefillDrugs.iCurrentMedicationStatus == 1){
                                sStyle = "style='background-color:#ffe5a2;'";
                            }
                            sRefillTemplate += '<tr '+sStyle+'>';
                                sRefillTemplate += '<td>'+aRefillDrugs.drug_code+'</td>';
                                sRefillTemplate += '<td>'+aRefillDrugs.generic_name+'</td>';
                                sRefillTemplate += '<td>'+aRefillDrugs.drug_name+'</td>';
                                sRefillTemplate += '<td>'+aRefillDrugs.formulation+'</td>';
                                sRefillTemplate += '<td>'+aRefillDrugs.strength+'</td>';
                                sRefillTemplate += '<td>'+aRefillDrugs.frequency_name+'</td>';
                                sRefillTemplate += '<td>'+((aRefillDrugs.prescription_dose == null || aRefillDrugs.prescription_dose == '') ? 'NA' : aRefillDrugs.prescription_dose)+'</td>';
                                sRefillTemplate += '<td>'+((aRefillDrugs.instructions == null || aRefillDrugs.instructions == '') ? 'NA' : aRefillDrugs.instructions)+'</td>';
                                sRefillTemplate += '<td>'+aRefillDrugs.creation_date+'</td>';
                                sRefillTemplate += '<td>'+aRefillDrugs.dispense_status+'</td>';
                                sRefillTemplate += '<td><input type="checkbox" class="classSelectPatientRefillDrugs" data-drug-id="'+aRefillDrugs.drug_id+'"/></td>';
                            sRefillTemplate += '</tr>';
                        });
                        $("#idModalRefillPatientPrescription").find("#idTableBodyPatientRefillDrugListing").html(sRefillTemplate);
                    }else{
                        $("#idModalRefillPatientPrescription").find("#idTableBodyPatientRefillDrugListing").html("<tr><td colspan='11' style='text-align:center;'>No Data Found</td></tr>");
                    }
                }else{
                    $("#idModalRefillPatientPrescription").find("#idTableBodyPatientRefillDrugListing").html("<tr><td colspan='11' style='text-align:center;'>No Data Found</td></tr>");
                }
            }
        });
    }

    //! function for notify warning alert...
    function pNotifyPrescriptionAlert(sNotifyText, sNotifyType){
        new PNotify({
            'text': sNotifyText,
            'type': sNotifyType,
            'animation': 'none',
            'delay': 8000,
            'buttons':{
                'sticker': false
            }
        });
    }    

    // to get prescription template prescriptions
    function refillPrescriptionDrugs(aDrugID = []){
        if (aDrugID.length > 0) {
            var iExistingPrescription = oPrescriptionPad.getPrescriptionItemsCount();
            $.each(aDrugID, function(iRowIndex, iDrugID){
                if ($.isEmptyObject(aRefilledDrugsContainer[iDrugID]) == false) {
                    var iIndex = iExistingPrescription + parseInt(iRowIndex);
                    oPrescriptionDrugItem = new PrescriptionDrugItem();
                    var iDrugSelected =0;
                    oDrug = new Drug(aRefilledDrugsContainer[iDrugID]);
                    oPrescriptionDrugItem.addDrug(oDrug,0);
                    oPrescriptionDrugItem.setSelectedDrug(iDrugSelected);
                    oPrescriptionDrugItem.setEndDate("");
                    oPrescriptionDrugItem.setStartDate("");
                    oPrescriptionDrugItem.setDrugDosage(aRefilledDrugsContainer[iDrugID]['dosage']);
                    oPrescriptionDrugItem.setFormulation(aRefilledDrugsContainer[iDrugID]['formulation']);
                    oPrescriptionDrugItem.setDose(aRefilledDrugsContainer[iDrugID]['prescription_dose']);
                    oPrescriptionDrugItem.setNoOfTablets(aRefilledDrugsContainer[iDrugID]['no_of_tablets']);
                    oPrescriptionDrugItem.validatePrescriptionItem();
                    oPrescriptionPad.addPrescriptonDrugItem(iIndex,oPrescriptionDrugItem);
                    oPrescriptionPad.setPrescriptionDrugAvailability(iIndex,aRefilledDrugsContainer[iDrugID]['drug_availability_status']);
                    oRenderer.setPrescriptionAvailabilityStatus(iIndex);
                    oRenderer.createPrescriptionUI();
                    oRenderer.refreshPrescriptionView();                    
                }
            });
        } else {
            pNotifyPrescriptionAlert('Please select proper drug(s) to refill.', 'error');
        }

        $("#idModalRefillPatientPrescription").modal('hide');
    }// get Prescription item details

    //! Function for fetching in-patient history container..
    function getInConsultationPrescriptionHistory(iConsultationID) {
        $.ajax({
            type: 'POST',
            url: "ajaxEhr.php?sFlag=getInConsultationPrescriptionHistory",
            data: {iConsultationID:iConsultationID},
            beforeSend: function(){
                $(document).find("#idModalInConsultationPrescriptionHistory").LoadingOverlay("show");
            },
            success: function (data){
                if($.trim(data) != false){
                    if ($.isEmptyObject(data) == false) {
                        var sTemplate = '';
                        $.each(data, function(iSetID, aPrescriptionSetDetails) {
                            sTemplate += '<div class="row-fluid">';
                                sTemplate += '<div class="span3"><strong>Prescribed By : </strong></div>';
                                sTemplate += '<div class="span3"><strong>'+(aPrescriptionSetDetails.added_by)+'</strong></div>';
                                sTemplate += '<div class="span3"><strong>Prescribed On : </strong></div>';
                                sTemplate += '<div class="span3"><strong>'+(aPrescriptionSetDetails.added_on)+'</strong></div>';
                            sTemplate += '</div>';
                            if (aPrescriptionSetDetails['prescribed_drug_details'].length > 0) {
                                sTemplate += '<div class="row-fluid">';
                                    sTemplate += '<table class="table table-condensed table-bordered classTableView" style="margin:5px;">';
                                        sTemplate += '<thead>';
                                            sTemplate += '<tr class="classTableHeader" id="idTableHeader">';
                                                sTemplate += '<td style="text-align:center;"><strong>Drug Name</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Generic Name</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Strength</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Formulation</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Frequency</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Start Date</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>End Date</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Days</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Dose</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Qty</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Is Stopped?</strong></td>';
                                                sTemplate += '<td style="text-align:center;"><strong>Action</strong></td>';
                                            sTemplate += '</tr>';
                                        sTemplate += '</thead>';
                                        sTemplate += '<tbody>';
                                            $.each(aPrescriptionSetDetails['prescribed_drug_details'], function(iIndex, aPrescribedDrugDetails) {
                                                sTemplate += '<tr>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.drug_name)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.generic_name)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.strength)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.formulation)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.frequency_name)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.start_date)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.end_date)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.days)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.prescription_dose)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.no_of_tablets)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.is_stopped)+'</td>';
                                                    sTemplate += '<td style="text-align:center;">'+(aPrescribedDrugDetails.action_name)+'</td>';
                                                sTemplate += '</tr>';
                                            });
                                        sTemplate += '</tbody>';
                                    sTemplate += '</table>';
                                sTemplate += '</div>';
                            }
                            sTemplate += '<hr>';
                        });
                        
                        //! Append..
                        $("#idModalInConsultationPrescriptionHistory").find(".modal-body").html(sTemplate);
                    }
                }
            },
            complete: function(){
                $(document).find("#idModalInConsultationPrescriptionHistory").LoadingOverlay("hide");
            },
        });
    }

    $(document).ready(function(){
        oPrescriptionPad.setPrescriptionStartDate(0,moment().format('DD-MM-YYYY'));
        oPrescriptionPad.setPrescriptionEndDate(0,moment().format('DD-MM-YYYY'));
        oPrescriptionPad.setPrescriptionNoOfDays(0,1);
    });
</script>
<script src="js/PrescriptionPad.js?ver=1.0"></script>
<script src="js/PrescriptionPadRenderer.js?ver=1.0"></script>

<?php
    if (FEATURE_OFFLINE_HELPER) {
        ?>

        <script type="text/javascript">

            //To get prescription helper data
            function fGetLSNewPrescriptionHelperData(iHelperID){
                var aPrescriptionData = {};
                var aPrescriptionLSData = [];

                var iPrescriptionEmergencyStatus = ($('#idPrescriptionContainer').find('#idIsEmergency').is(':checked') == true) ? 1 : 0;

                //! For gets all the prescription drug values
                $('#idPrescriptionContainer').find('select[name="idGenericName[]"]').each(function(){

                    var iRowIndex = $(this).data('row');

                    if (iRowIndex > 0) {
                        var iDrugID = $('.classHelperEmbed_prescription').find('#idDrugName'+iRowIndex).val(),
                            sDrugName = $('.classHelperEmbed_prescription').find("#idDrugName"+iRowIndex+" option:selected").text(),
                            sGenericName = $('.classHelperEmbed_prescription').find("#idGenericName"+iRowIndex+" option:selected").text(),
                            sFormulation = $('.classHelperEmbed_prescription').find("#idFormulation"+iRowIndex).val(),
                            sStrength = $('.classHelperEmbed_prescription').find("#idStrength"+iRowIndex+" option:selected").text(),
                            sFrequency = $('.classHelperEmbed_prescription').find("#idDosage"+iRowIndex).val(),
                            iROA = $('.classHelperEmbed_prescription').find("#idRouteOfAdministration"+iRowIndex).val(),
                            sROA = $('.classHelperEmbed_prescription').find("#idRouteOfAdministration"+iRowIndex+" option:selected").text(),
                            dStartDate = $('.classHelperEmbed_prescription').find("#idStartDate"+iRowIndex).val(),
                            dEndDate = $('.classHelperEmbed_prescription').find("#idEndDate"+iRowIndex).val(),
                            iNoOfDays = $('.classHelperEmbed_prescription').find("#idNoOfDays"+iRowIndex).val(),
                            iNoOfTablets = $('.classHelperEmbed_prescription').find("#idNoOfTablets"+iRowIndex).val(),
                            iDose = $('.classHelperEmbed_prescription').find("#idDose"+iRowIndex).val(),
                            iIsStopped = $('.classHelperEmbed_prescription').find("#idPrescriptionStatus"+iRowIndex).val(),
                            sInstructions = $('.classHelperEmbed_prescription').find("#idInstructions"+iRowIndex).val(),
                            sInteraction = $('.classHelperEmbed_prescription').find("#idInteractions"+iRowIndex).data('content'),
                            sContraIndication = $('.classHelperEmbed_prescription').find("#idContraIndications"+iRowIndex).data('content'),
                            sSideEffects = $('.classHelperEmbed_prescription').find("#idSideEffects"+iRowIndex).data('content'),
                            sFrequency = (sFrequency != null && sFrequency != '') ? sFrequency.split(",")[0] : '';


                            if (iDrugID > 0) {

                                aPrescriptionLSData.push({
                                    'iDrugID' : iDrugID, 
                                    'sDrugName' : sDrugName, 
                                    'sGenericName' : sGenericName, 
                                    'sFormulation' : sFormulation, 
                                    'sStrength' : sStrength, 
                                    'sFrequency' : sFrequency, 
                                    'sInstructions' : sInstructions, 
                                    'iROA' : iROA, 
                                    'sROA' : sROA, 
                                    'dStartDate' : dStartDate,
                                    'dEndDate' : dEndDate,
                                    'iNoOfDays' : iNoOfDays,
                                    'iNoOfTablets' : iNoOfTablets,
                                    'iDose' : iDose,
                                    'sInteraction' : sInteraction,
                                    'sContraIndication' : sContraIndication,
                                    'sSideEffects' : sSideEffects,
                                    'iIsStopped' : iIsStopped,
                                });
                            }
                    }
                });

                //Capture the data
                if(aPrescriptionLSData.length > 0){
                    aPrescriptionData = {
                        'aPrescriptionLSData': aPrescriptionLSData,
                        'iPrescriptionEmergencyStatus': iPrescriptionEmergencyStatus
                    };
                }

                if($.isEmptyObject(aPrescriptionData)){
                    return null;
                }else{
                    return aPrescriptionData;
                }
            }

            //To set prescription helper data
            function fSetLSNewPrescriptionHelperData(iHelperID, aLocalStorageHelperData){

                var aPrescriptionDrugInfo = aLocalStorageHelperData.aPrescriptionLSData;
                var iIsEmergency = aLocalStorageHelperData.iPrescriptionEmergencyStatus;
                oPrescriptionPad.aPrescriptionItems = [];
                var iExistingRowCount = oPrescriptionPad.getPrescriptionItemsCount();

                $.each(aPrescriptionDrugInfo, function(iIndex, aSelectedDrugInfo) {
                    var drug_id = aSelectedDrugInfo.iDrugID,
                        drug_code = '',
                        drug_name = aSelectedDrugInfo.sDrugName,
                        generic_name = aSelectedDrugInfo.sGenericName,
                        formulation = aSelectedDrugInfo.sFormulation,
                        strength = aSelectedDrugInfo.sStrength,
                        dosage = aSelectedDrugInfo.sFrequency,
                        drug_for = '',
                        instructions = aSelectedDrugInfo.sInstructions,
                        drug_interaction = aSelectedDrugInfo.sInteraction,
                        contra_indication = aSelectedDrugInfo.sContraIndication,
                        side_effects = aSelectedDrugInfo.sSideEffects,
                        category = '',
                        manufacturer = '',
                        product_cost = '',
                        product_price = '',
                        route_of_administration_id = aSelectedDrugInfo.iROA,
                        route_of_administration = aSelectedDrugInfo.sROA,
                        specialisation = '',
                        start_date = aSelectedDrugInfo.dStartDate,
                        end_date = aSelectedDrugInfo.dEndDate,
                        no_of_days = aSelectedDrugInfo.iNoOfDays,
                        no_of_tablets = aSelectedDrugInfo.iNoOfTablets,
                        dose = aSelectedDrugInfo.iDose,
                        is_stopped = aSelectedDrugInfo.iIsStopped,
                        iRow = iExistingRowCount + parseInt(iIndex);

                        //! Rendering selected items..
                        oPrescriptionDrugItem = new PrescriptionDrugItem();
                        var iDrugSelected = 0;

                        oDrug = new Drug({
                            'drug_id' : drug_id,
                            'drug_code' : drug_code,
                            'drug_name' : drug_name,
                            'generic_name' : generic_name,
                            'formulation' : formulation,
                            'strength' : strength,
                            'dosage' : dosage,
                            'drug_for' : drug_for,
                            'instructions' : instructions,
                            'drug_interaction' : drug_interaction,
                            'contra_indication' : contra_indication,
                            'side_effects' : side_effects,
                            'category' : category,
                            'manufacturer' : manufacturer,
                            'product_cost' : product_cost,
                            'product_price' : product_price,
                            'route_of_administration_id' : route_of_administration_id,
                            'route_of_administration' : route_of_administration,
                            'specialisation' : specialisation,
                        });

                        oPrescriptionDrugItem.addDrug(oDrug,iRow);
                        oPrescriptionDrugItem.setSelectedDrug(iDrugSelected);
                        oPrescriptionDrugItem.setEndDate(end_date);
                        oPrescriptionDrugItem.setStartDate(start_date);
                        oPrescriptionDrugItem.setDrugDosage(dosage);
                        oPrescriptionDrugItem.setFormulation(formulation);
                        oPrescriptionDrugItem.setDose(dose);
                        oPrescriptionDrugItem.setNoOfTablets(no_of_tablets);
                        oPrescriptionDrugItem.setIsStopped(is_stopped);
                        oPrescriptionDrugItem.setROA(route_of_administration_id);                                
                        oPrescriptionDrugItem.setROAName(route_of_administration);                                
                        oPrescriptionDrugItem.setDrugInstructions(instructions);                        

                        oPrescriptionDrugItem.validatePrescriptionItem();     
                        oPrescriptionPad.addPrescriptonDrugItem(iRow,oPrescriptionDrugItem);

                        getDrugAvailabilityByDrugId(drug_id,iRow);
                });

                oRenderer.refreshPrescriptionView();
                // refresh the view
                if (iIsEmergency == 1) {
                    $(".classDivSetAsEmergencyBlock").find("#idIsEmergency").prop('checked', true).change();
                } else {
                    $(".classDivSetAsEmergencyBlock").find("#idIsEmergency").prop('checked', false).change();
                }
            }

            // preview prescription helper data
            function fPreviewNewPrescriptionHelperData(aLocalStorageHelperData) {

                var aPreviewDataContainer = [];
                var aPreviewData = [];
                aPreviewData.aValues = [];

                var aPrescriptionDrugInfo = aLocalStorageHelperData.aPrescriptionLSData;
                var iIsEmergency = aLocalStorageHelperData.iPrescriptionEmergencyStatus;

                aPreviewData.aHeadings = ['Sr No','Generic Name','Drug Name','Formulation',
                        'Strength','Frequency','ROA','Start Date','End Date','No Of Days',
                        'No Of Tablets','Dose','Instructions'];

                for(var iii = 0; iii < aPrescriptionDrugInfo.length;iii++){

                    var sGenericName  = aPrescriptionDrugInfo[iii].sGenericName;
                    var sDrugName  = aPrescriptionDrugInfo[iii].sDrugName;
                    var sFormulation  = aPrescriptionDrugInfo[iii].sFormulation;
                    var sStrength  = aPrescriptionDrugInfo[iii].sStrength;
                    var sFrequency  = aPrescriptionDrugInfo[iii].sFrequency;
                    var sROA  = aPrescriptionDrugInfo[iii].sROA;
                    var dStartDate  = aPrescriptionDrugInfo[iii].dStartDate;
                    var dEndDate  = aPrescriptionDrugInfo[iii].dEndDate;
                    var iNoOfDays  = aPrescriptionDrugInfo[iii].iNoOfDays;
                    var iNoOfTablets  = aPrescriptionDrugInfo[iii].iNoOfTablets;
                    var iDose  = aPrescriptionDrugInfo[iii].iDose;
                    var sInstructions  = aPrescriptionDrugInfo[iii].sInstructions;
                    var iSrNo = iii+1;

                    aPreviewData.aValues.push([iSrNo, sGenericName, sDrugName,sFormulation,sStrength,sFrequency,sROA,dStartDate,dEndDate,iNoOfDays,iNoOfTablets,iDose,sInstructions]);
                }

                aPreviewDataContainer.push(aPreviewData);

                return aPreviewDataContainer;
            }

        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                oHelperOfflineStorageManager.registerHelper({
                    "iHelperID": <?php echo $iHelperID; ?>,
                    "iScheduleID" : <?php echo $iScheduleID; ?>,
                    "iServiceID": <?php echo $iServiceID; ?>,
                    "fHelperDataGetter": fGetLSNewPrescriptionHelperData,
                    "fHelperDataSetter": fSetLSNewPrescriptionHelperData,
                    "fHelperDataPreviewDataGenerator": fPreviewNewPrescriptionHelperData
                });
            });
        </script>
        <?php
    }
?>