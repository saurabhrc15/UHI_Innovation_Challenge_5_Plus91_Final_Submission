function PrescriptionPadRenderer(){
	this.iPrescriptionCount=1;
	this.allowNewDrugsToBeAdded = false;
	this.allowNewDrugsToBeCreated = false;

	// to fill Prescription fields
	this.fillPrescriptionFields = function fillPrescriptionFields(iIndex){

		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex-1);
		const iDrugId = isNaN(parseInt(thisPrescription['iDrugId']) || parseInt(thisPrescription['iDrugId']) == 0) ? thisPrescription['sGenericName'] : parseInt(thisPrescription['iDrugId']);
		const sGenericName = (isNaN(parseInt(iDrugId)) || parseInt(iDrugId) == 0 ) ? thisPrescription['sGenericName'] : iDrugId;
		const sDrugName = (isNaN(parseInt(iDrugId)) || parseInt(iDrugId) == 0 ) ? thisPrescription['sDrugName'] : iDrugId;
		$('#idGenericName'+iRowIndex).html("<option value='"+sGenericName+"'>"+thisPrescription['sGenericName']+"</option>");
		$('#idDrugName'+iRowIndex).html("<option value='"+sDrugName+"'>"+thisPrescription['sDrugName']+"</option>");
		$('#idInstructions'+iRowIndex).val(thisPrescription['sInstructions']);

		// to enable pop overs
		$('[data-toggle="popover"]').popover();

		this.setInteractions(iIndex);

		if (thisPrescription['sInteractions'] != "" && thisPrescription['sInteractions'] != undefined) {
			$('#idContraIndications' + iRowIndex).removeAttr('disabled');
			$('#idContraIndications' + iRowIndex).attr('data-content', thisPrescription['sContraIndications']);
		}

		if (thisPrescription['sSideEffects'] != "" && thisPrescription['sSideEffects'] != undefined) {
			$('#idSideEffects' + iRowIndex).removeAttr('disabled');
			$('#idSideEffects' + iRowIndex).attr('data-content', thisPrescription['sSideEffects']);
		}

		if(thisPrescription['dStartDate']!=""){
			$('#idStartDate'+iRowIndex).val(thisPrescription['dStartDate']);
		}

		if(thisPrescription['dEndDate']!=""){
			$('#idEndDate'+iRowIndex).val(thisPrescription['dEndDate']);
		}

		$('#idNoOfDays'+iIndex).val(thisPrescription['iNoOfDays']);
		$('#idNoOfTablets'+iIndex).val(thisPrescription['iNoOfTablets']);
		$('#idDose'+iIndex).val(thisPrescription['iDose']);
		
		//set formulation 
		this.setPrescriptionFormulation(iIndex);

		//set strength
		this.setPrescriptionStrength(iIndex);

		// set prescription dosage
		this.setPrescriptionDosage(iIndex);

		// set is prescription stopped
		this.setIsPrescriptionStopped(iIndex);

		// set prescription roa..
		this.setPrescriptionROA(iIndex);

		// set stopping reason
		this.setStoppingReason(iIndex);

		// frequency details
		this.setPrescriptionFreqencyetails(iIndex);

	}

	this.setStoppingReason = function(iIndex)
	{	
		let iRowIndex = parseInt(iIndex);
		let thisPrescription = oPrescriptionPad.aPrescriptionItems[iIndex-1];
		if(thisPrescription['oStoppingReason']['sReason']!="" && thisPrescription['oStoppingReason']['sReason']!=undefined){
			$(`#idStopResetPrescription${iRowIndex}`).attr('data-stop-reason',thisPrescription['oStoppingReason']['sReason']);
			$(`#idStopResetPrescription${iRowIndex}`).attr('data-user',thisPrescription['oStoppingReason']['sStoppedBy']);
			$(`#idStopResetPrescription${iRowIndex}`).attr('data-datetime',thisPrescription['oStoppingReason']['dStoppedAt']);
		}
		else{
			$(`#idStopResetPrescription${iRowIndex}`).removeClass('classPopoverPrescriptionInfo');
		}
	}

	this.setInteractions = function setInteractions(iIndex) {
		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex - 1);
		var sContent = "";

		var aContent = [];

		if (thisPrescription['aInteractionDrugs'] && thisPrescription['aInteractionDrugs'].length) {
			aContent.push("<strong>Drugs:</strong><br/><ul>" + $.map(thisPrescription['aInteractionDrugs'], function (oInteractionDrug, iIndex) {
				return "<li>"+oInteractionDrug.name+"</li>";
			}).join("")+"</ul>");
		}

		if (thisPrescription['sInteractions'] != "" && thisPrescription['sInteractions'] != undefined) {
			aContent.push("<strong>Non Drugs:</strong><br/>" + thisPrescription['sInteractions']);
		}

		if (aContent.length) {
			$('#idInteractions' + iRowIndex).removeAttr('disabled');
			$('#idInteractions' + iRowIndex).attr('data-content', aContent.join("<br/>"));
		}
	}

	// to set prescription drug availabliity
	this.setPrescriptionAvailabilityStatus = function setPrescriptionAvailabilityStatus(iIndex){
		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex-1);

		if(thisPrescription['sAvailablity'] == "Available"){
			$('#idDrugAvailability'+iRowIndex).removeAttr('disabled');
			$('#idDrugAvailability'+iRowIndex).attr('title',thisPrescription['sAvailablity']);
			$('#idHiddenDrugAvailability'+iRowIndex).attr('value','Available');
		}else{
			$('#idDrugAvailability'+iRowIndex).attr('disabled','disabled');
			$('#idDrugAvailability'+iRowIndex).attr('title',thisPrescription['sAvailablity']);
			$('#idHiddenDrugAvailability'+iRowIndex).attr('value','Not Available');
		}
	}

	// to set prescription formulation
	this.setPrescriptionFormulation = function setPrescriptionFormulation(iIndex){

		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex-1);
		var sTemplate = "";
		var sFormulation="";
		sTemplate+="<option value=''>Select Any</option>";
		for(var iii =0 ; iii < aPrescriptionDrugFormulations.length; ++iii){
			sFormulation = thisPrescription['sFormulation'];
			if(sFormulation.toLowerCase() == aPrescriptionDrugFormulations[iii]['formulation'].toLowerCase() ){
		        sTemplate+="<option value="+sFormulation+" selected >"+sFormulation+"</option>";
		    }else{
		        sTemplate+="<option value="+aPrescriptionDrugFormulations[iii]['formulation']+">"+aPrescriptionDrugFormulations[iii]['formulation']+"</option>";
		        
		    }
		}
		$('#idFormulation'+iRowIndex).html(sTemplate);
	}

	// to set prescription roa
	this.setPrescriptionROA = function setPrescriptionROA(iIndex){

		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex-1);
		var sTemplate = "";
		var sROA="";
		var iROA=0;
		sTemplate+="<option value=''>Select</option>";
		for(var iii =0 ; iii < aPrescriptionDrugROA.length; ++iii){
			var sSelected = "";

			if(thisPrescription['iROA'] == aPrescriptionDrugROA[iii]['id'] ){
				sSelected = "selected";
		    }
		    
		    sTemplate+="<option value="+aPrescriptionDrugROA[iii]['id']+" "+sSelected+">"+aPrescriptionDrugROA[iii]['route_of_administration']+"</option>";
		}
		$('#idRouteOfAdministration'+iRowIndex).html(sTemplate);
	}

	// to set prescription strength
	this.setPrescriptionStrength = function setPrescriptionStrength(iIndex){
		
		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItemObject(iIndex-1);
		var aStrengths  = thisPrescription.getAllDrugStrength();
		var iDrugSelected = thisPrescription.iDrugSelected;
		const oDrug = thisPrescription.aDrugItems[iDrugSelected];
		const sStrength = oDrug.sStrength;

		var sTemplate = "";

		for(var iii=0; iii < aStrengths.length; ++iii){
			if(thisPrescription.iDrugSelected == iii){
				sTemplate+= "<option value = "+iii+" selected >"+aStrengths[iii]['strength']+"</option>";
			}else{
				sTemplate+= "<option value = "+iii+">"+aStrengths[iii]['strength']+"</option>";
			}
		}

		if (parseInt(oDrug.iDrugId) === 0 && this.allowNewDrugsToBeCreated === false ) {
			sTemplate = `<option value="${sStrength}">${sStrength}</option>`;
		}

		$('#idStrength'+iRowIndex).html(sTemplate);

		if (thisPrescription.bIsNewDrug) {
			$('#idStrength'+iRowIndex).html(`<option value = "${aStrengths[0]['strength']}">${aStrengths[0]['strength']}</option>`);
			$('#idStrength'+iRowIndex).select2({tags: true});
		}
	}

	// set prescription dosage
	this.setPrescriptionDosage = function setPrescriptionDosage(iIndex){
		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex-1);
		var sTemplate = "";
		var sDosage=thisPrescription['sDosage'];

		sTemplate+="<option value=''>Select</option>";
		for(var iii =0 ; iii < aPrescriptionDrugFrequencies.length; ++iii){
			var iId =  aPrescriptionDrugFrequencies[iii]['id'];
			var sActualFrequncy = aPrescriptionDrugFrequencies[iii]['actual_frequency'];
			var sActualDosage = aPrescriptionDrugFrequencies[iii]['frequency_name'];
			
			var sValue = iId+","+sActualFrequncy+","+sActualDosage;

			if(sDosage == iId){
		        sTemplate+="<option value="+sValue+" selected >"+sActualDosage+"</option>";
		    }else{
		        sTemplate+="<option value="+sValue+">"+sActualDosage+"</option>";
		        
		    }
		}

		$('#idDosage'+iRowIndex).html(sTemplate);
		
		// if dosage is empty, set first dosage by default
		/*if(sDosage==""){
			oPrescriptionPad.setPrescriptionDrugDosage(iIndex-1,aPrescriptionDrugFrequencies[0]['id']);
		}*/
	}

	// set prescription dosage
	this.setPrescriptionFreqencyetails = function setPrescriptionFreqencyetails(iIndex){
		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex-1);
		var sTemplate = "";

		if($('#idPrescriptionFrequencyDetails').is(':empty')){

			for(var iii =0 ; iii < aPrescriptionDrugFrequencies.length; ++iii){

				var iId =  aPrescriptionDrugFrequencies[iii]['id'];
				var sFrequencyCategory = aPrescriptionDrugFrequencies[iii]['frequency_category'];
				var iTimeBetweenDosage = aPrescriptionDrugFrequencies[iii]['time_between_dosage'];
				var iDosageCountInDay = aPrescriptionDrugFrequencies[iii]['dosage_count_in_day'];

				sTemplate += '<input type="hidden" id="idFrequencyCategory_'+iId+'" name="idFrequencyCategory_'+iId+'" value="'+sFrequencyCategory+'"/>';
				sTemplate += '<input type="hidden" id="idFrequencyTimeBetwwenDosage_'+iId+'" name="idFrequencyTimeBetwwenDosage_'+iId+'" value="'+iTimeBetweenDosage+'"/>';
				sTemplate += '<input type="hidden" id="idDosageCountInDay_'+iId+'" name="idDosageCountInDay_'+iId+'" value="'+iDosageCountInDay+'"/>';
			}

			$('#idPrescriptionFrequencyDetails').html(sTemplate);
		}
	}

	// to set is prescription stopped
	this.setIsPrescriptionStopped = function setIsPrescriptionStopped(iIndex){
		var iRowIndex = parseInt(iIndex);
		var thisPrescription = oPrescriptionPad.getPrescriptionDrugItem(iIndex-1);

		if(thisPrescription['iIsStopped']==1){
			$('#idStopResetPrescription'+iRowIndex).removeClass("btn-success");
            $('#idStopResetPrescription'+iRowIndex).addClass("btn-danger");
            $('#idStopResetPrescription'+iRowIndex).attr("title","Restart Prescription");
            $("#idPrescriptionStatus"+iRowIndex).val('1');
		}else{
			$('#idStopResetPrescription'+iRowIndex).removeClass("btn-danger");
			$('#idStopResetPrescription'+iRowIndex).addClass("btn-success");
			$('#idStopResetPrescription'+iRowIndex).attr("title","Stop Prescription");
			$("#idPrescriptionStatus"+iRowIndex).val('0');
		}
	}

	// to refresh the prescription item 
	this.refreshPrescriptionView = function refreshPrescriptionView(){
		$('#idPrescriptionContainer').empty();
		for(var iii=0 ; iii < oPrescriptionPad.getPrescriptionItemsCount(); ++iii){
			var iRowIndex = parseInt(iii)+1;
			this.createPrescriptionUI();
			this.fillPrescriptionFields(iRowIndex);
		}
	}

	// to clear input fields when a drug is selected
	this.clearFields = function clearFields(iIndex){
		var iRowIndex = parseInt(iIndex);
		$('#idStartDate'+iRowIndex).val('');
		$('#idEndDate'+iRowIndex).val('');		
	}

	this.createPrescriptionUI = function createPrescriptionUI(){

		var iCount = $(".classPrescriptionHelper").find("#idPrescriptionContainer").children().length;
		var iPrescriptionIndex = iCount+1;

		if( oPrescriptionPad.getPrescriptionItemsCount() == 0){
			iPrescriptionIndex=1;
		}
		
		var blankClone = $([
		    '<div class="classPrescription" id="idPrescription'+iPrescriptionIndex+'" class="classPrescription">',
		        '<input type="hidden" name="idHiddenDrugAvailability[]" id="idHiddenDrugAvailability'+iPrescriptionIndex+'">',
		        '<input type="hidden" id="idPrescriptionStatus'+iPrescriptionIndex+'" name="idPrescriptionStatus[]" value="0"/>',
		        '<div id="idPrescriptionFrequencyDetails"></div>',
		        '<div class="row-fluid">',
		            '<div class="span6">',
		                '<label class="classLabel"><small>Generic Name</small></label>',
		                '<select type="text" class="classPrescriptionDrugName" name="idGenericName[]" id="idGenericName'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'"style="width:100%;"></select>',
		            '</div>',
		            '<div class="span6">',
		                '<label class="classLabel"><small>Drug Name</small></label>',
		                '<select type="text" class="classPrescriptionDrugName"" name="idDrugName[]" data-row="'+iPrescriptionIndex+'" id="idDrugName'+iPrescriptionIndex+'" style="width:100%;"></select>',
		            '</div>',
		        '</div>',
		        '<div class="row-fluid">',
		            '<div class="span2">',
		                '<label class="classLabel"><small>Formulation</small></label>',
		                '<select  class="classSelectBoxXS classFormulation classCalculateNoOfTablets" name="idFormulation[]" id="idFormulation'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" style="width:100%;"></select>',
		            '</div>',
		            '<div class="span2">',
		                '<label class="classLabel"><small>Strength</small></label>',
		                '<select class="classSelectBoxXS classDrugStrength classCalculateNoOfTablets" name="idStrength[]" id="idStrength'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" style="width:100%;"></select>',
		            '</div>',
		            '<div class="span2">',
		                '<label class="classLabel"><small>Frequency</small></label>',
		                '<select  class="classInputTextXXS classPrescriptionDosage classCalculateNoOfTablets" type="text" name="idDosage[]" id="idDosage'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" style="width:100%;"></select>',
		            '</div>',
		            '<div class="span2">',
		                '<label class="classLabel"><small>ROA</small></label>',
		                '<select class="classSelectBox classRouteOfAdministration" name="idRouteOfAdministration[]" id="idRouteOfAdministration'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" style="width:100%;"></select>',
		            '</div>',
		       		'<div class="span2">',
		                '<label class="classLabel"><small>Start Date</small></label>',
		                '<input  class="classInputText classPrescriptionDate classCalculateNoOfTablets" type="text" name="idStartDate[]" id="idStartDate'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" readonly="readonly" style="width:100%;"/>',
		            '</div>',
		            '<div class="span2">',
		                '<label class="classLabel"><small>End Date</small></label>',
		                '<input  class="classInputText classPrescriptionDate classCalculateNoOfTablets" type="text" name="idEndDate[]" id="idEndDate'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" readonly="readonly" style="width:100%;"/>',
		            '</div>',
		       	'</div>',
		        '<div class="row-fluid">',
		            '<div class="span2">',
		                '<label class="classLabel"><small>Days</small></label>',
		                '<input  class="classInputText classDays classCalculateNoOfTablets" type="text" name="idNoOfDays[]" id="idNoOfDays'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" style="width:100%;"/>',
		            '</div>',
		            '<div class="span2">',
		                '<label class="classLabel"><small>Quantity</small></label>',
		                '<input  class="classInputTextXXS classNoOfTablets" type="text" name="idNoOfTablets[]" id="idNoOfTablets'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" style="width:100%;"/>',
		            '</div>',
		            '<div class="span2">',
		                '<label class="classLabel"><small>Dose</small></label>',
		                '<input type="text" class="classInputText classDose classCalculateNoOfTablets" name="idDose[]" id="idDose'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" style="width:100%;"/>',
		            '</div>',
		            '<div class="span6" style="margin-top:25px;">',
                        '<a class="btn btn-danger classDeletePrescription" name="idRemovePrescription[]" id="idRemovePrescription'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" title="Move to Trash" style="cursor:pointer;"><i class="fa fa-trash"></i></a>',
                        '<a class="btn btn-primary classPopup" data-html="true" name="idInteractions[]" id="idInteractions'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" title="Interactions" data-toggle="popover" data-trigger="hover" data-content="" data-placement="bottom" disabled style="margin-left:5px; cursor:pointer;"><i class="fa fa-info" aria-hidden="true"></i></a>',
                        '<a class="btn btn-primary classPopup" name="idContraIndications[]" id="idContraIndications'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" title="Contra-Indications" data-toggle="popover" data-trigger="hover" data-content="" data-placement="bottom"  disabled style="margin-left:5px; cursor:pointer;"><i class="fa fa-times-circle-o" aria-hidden="true"></i></a>',
                        '<a class="btn btn-primary classPopup" name="idSideEffects[]" id="idSideEffects'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" title="Side Effects" data-toggle="popover" data-trigger="hover" data-content="" data-placement="bottom" disabled style="margin-left:5px; cursor:pointer;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></a>',
                        '<a class="btn btn-primary classPopup" name="idDrugAvailability[]" id="idDrugAvailability'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" title="Availability" value="none" disabled style="margin-left:5px; cursor:pointer;"><i class="fa fa-check-square" aria-hidden="true"></i></a>',
                        '<a class="btn btn-success classStopResetPrescription classPopoverPrescriptionInfo" id="idStopResetPrescription'+iPrescriptionIndex+'" data-popover-content="#idPopoverPrescriptionInfo" data-row-id="'+iPrescriptionIndex+'"  data-stop-reason="" data-user="" data-datetime="" title="Stop Prescription" style="margin-left:5px; cursor:pointer;"><i class="fa fa-ban" aria-hidden="true"></i></a>',	                    
                        '<input type="hidden" class="classStopPrescriptionReasonSave" name="idStopPrescriptionReasonSave[]" id="idStopPrescriptionReasonSave'+iPrescriptionIndex+'"  />',
	                '</div>',
		        '</div>',
		        '<div class="row-fluid">',
		        	'<div class="span12">',
		            	'<label class="classLabel"><small>Instructions</small></label>',
	                    '<textarea class="classTextArea classInstructions" name="idInstructions[]" id="idInstructions'+iPrescriptionIndex+'" data-row="'+iPrescriptionIndex+'" rows="2" style="width:100%;"></textarea>',
	                '</div>',
		        '</div>',
		        '<hr class="classHRSize">',
		    '</div>'
		].join(""));
		var iClone = blankClone.clone();
		++this.iPrescriptionCount;

		$(".classPrescriptionHelper").find('#idPrescriptionContainer').append(iClone);

		// for pop overs
		$('[data-toggle="popover"]').popover();

		//attach date picker
		$(".classPrescriptionHelper").find('.classPrescriptionDate').datepicker({
		    changeMonth: true,
		    dateFormat: 'dd-mm-yy',
		    changeYear: true
		});

		$('#idStartDate'+iPrescriptionIndex).datepicker('setDate',moment().format('DD-MM-YYYY'));
		$('#idEndDate'+iPrescriptionIndex).datepicker('setDate',moment().format('DD-MM-YYYY'));
		
		$(".classPrescriptionHelper").find('.classFormulation').select2();
		$(".classPrescriptionHelper").find('.classDrugStrength').select2();
		$(".classPrescriptionHelper").find('.classPrescriptionDosage').select2();
		$(".classPrescriptionHelper").find('.classRouteOfAdministration').select2();

		//when searched by genereic name 
		$(".classPrescriptionHelper").find(".classPrescriptionDrugName").select2({                
		    width: '100%',
		    allowClear: false,
		    minimumInputLength: 3,
		    width: 'resolve',
		    tags: this.allowNewDrugsToBeAdded,
		    ajax: {
		        url:"ajaxEhr.php?sFlag=getAllDrugsByName",
		        data: function (params) {
		            return {
		                sDrugName: params.term,
		            };
		        },
		        processResults: function (data, params) {
		            var results=[];
		            aDrugsList.length=0;

		            for(var iii=0; iii< data.length; ++iii){
		                var sDrugName = (data[iii].drug_name==""?"NA":data[iii].drug_name);
		                var sGenericName = (data[iii].generic_name==""?"NA":data[iii].generic_name);
		                var sStrength = (data[iii].strength==""?"NA":data[iii].strength);
		                var sFormulation = (data[iii].formulation==""?"NA":data[iii].formulation);
		                
		                aDrugsList.push(data[iii]);
		                results.push({
		                    id: iii,
		                    text: sGenericName+"( "+sDrugName+" )[ "+sStrength+"-"+sFormulation+" ]"
		                });
		            }
		            return { results: results };
		        }
		    }
		});
	}

	this.setAllowDrugsToBeAdded = function(allowNewDrugsToBeAdded = false){
		this.allowNewDrugsToBeAdded = allowNewDrugsToBeAdded;
	}

	this.setAllowNewDrugsToBeCreated = function(allowNewDrugsToBeCreated = false){
		this.allowNewDrugsToBeCreated = allowNewDrugsToBeCreated;
	}
}