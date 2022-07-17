var aPrescriptionDrugFrequencies = [];
var aPrescriptionDrugFormulations = [];
var aDrugsList =[];
var aPrescriptionDrugROA =[];
var aPrescriptionCurrentMedication =[];
// No of tablets should only be calculated when Drug formulation is one from the below list.
var aRequiredPrescriptionFormulations = ["Tablet","Capsule","Suppository","Roll","Piece","Package","KIT","Vial"];

function PrescriptionPad(){
	this.aPrescriptionItems = [];
	this.allowNewDrugsToBeAdded = false;
	this.allowNewDrugsToBeCreated = false;
	
	// to add a PrescriptonDrugItem at a specified index
	this.addPrescriptonDrugItem = function addPrescriptonDrugItem(iIndex,oPrescriptionDrugItem){
		this.aPrescriptionItems[iIndex]=oPrescriptionDrugItem;
	}

	// delete a prescription item
	this.removePrescriptonDrugItem = function removePrescriptonDrugItem(iIndex){
		var aTemp = this.aPrescriptionItems;
		this.aPrescriptionItems=[];
		for(var iii = 0 ; iii < aTemp.length; ++iii){
			if(iii!=iIndex){
				this.aPrescriptionItems.push(aTemp[iii]);
			}
		}

	}

	// set frequencies for drugs .
	// aDrugFrequencies in format {id:"1","actual_frequency":"2","actual_dosage":"1-0-1"}
	this.setPrescriptionDrugDosageFrequencies = function setPrescriptionDrugDosageFrequencies(aDrugFrequencies){
		for(var iii=0; iii < aDrugFrequencies.length; ++iii){
			aPrescriptionDrugFrequencies.push(aDrugFrequencies[iii]);
		}
	}

	// set formulations for drug
	// aDrugFormulations in format {"key":"value"} eg. {"Tablet":"Tablet"}
	this.setPrescriptionDrugFormulations = function setPrescriptionDrugFormulations(aDrugFormulations){
		for(var iii=0; iii < aDrugFormulations.length; ++iii){
			aPrescriptionDrugFormulations.push(aDrugFormulations[iii]);
		}
	}

	//set drug formulation
	this.setPrescriptionDrugFormulation = function setPrescriptionDrugFormulation(iIndex,sFormulation){
		this.aPrescriptionItems[iIndex].setFormulation(sFormulation);
	} 

	// to change drug strength of a prescription
	this.setPrescriptionDrugStrength = function setPrescriptionDrugStrength(iIndex,iDrugSelected){
		this.aPrescriptionItems[iIndex].setSelectedDrug(iDrugSelected);
	}

	//set prescription dosage
	this.setPrescriptionDrugDosage = function setPrescriptionDrugDosage(iIndex,sDosage){
		this.aPrescriptionItems[iIndex].setDrugDosage(sDosage);
	}

	// set start date
	this.setPrescriptionStartDate = function setPrescriptionStartDate(iIndex,dStartDate){
		this.aPrescriptionItems[iIndex].setStartDate(dStartDate);
	}

	// set end date
	this.setPrescriptionEndDate = function setPrescriptionEndDate(iIndex,dEndDate){
		this.aPrescriptionItems[iIndex].setEndDate(dEndDate);
	}

	// set no of days
	this.setPrescriptionNoOfDays = function setPrescriptionNoOfDays(iIndex,iNoOfDays){
		this.aPrescriptionItems[iIndex].setNumberOfDaysManually(iNoOfDays);
	}

	//set No Of Tablets
	this.setPrescriptionNoOfTablets = function setPrescriptionNoOfTablets(iIndex,iNoOfTablets){
		this.aPrescriptionItems[iIndex].setNoOfTablets(iNoOfTablets);
	}

	// set prescription instructions
	this.setPrescriptionDrugInstructions = function  setPrescriptionDrugInstructions(iIndex,sInstructions){
		this.aPrescriptionItems[iIndex].setDrugInstructions(sInstructions);
	}

	//set prescription dose
	this.setPrescriptionDose = function setPrescriptionDose(iIndex,iDose){
		this.aPrescriptionItems[iIndex].setDose(iDose);
	}

	//set drug availability
	this.setPrescriptionDrugAvailability = function setPrescriptionDrugAvailability(iIndex,sAvailablity){
		this.aPrescriptionItems[iIndex].setDrugAvailability(sAvailablity);
	}

	this.setInteractionDrugs = function setInteractionDrugs(iIndex, aInteractionDrugs) {
		this.aPrescriptionItems[iIndex].setInteractionDrugs(aInteractionDrugs);
	}

	this.setStrength = function (iIndex, sStrength) {
		this.aPrescriptionItems[iIndex].setStrength(sStrength);
	}

	// to fetch details of all the prescriptions  
	this.getAllPrescriptionDrugItems = function getAllPrescriptionDrugItems(){
		var aAllPrescriptionItems = [];
		for(var iii=0; iii < this.aPrescriptionItems.length; ++iii){
			if(this.aPrescriptionItems[iii]!=null){
				aAllPrescriptionItems.push(this.aPrescriptionItems[iii].getPrescriptionDrugItemDetails()); 
			}
		}
		return aAllPrescriptionItems;
	}

	// to fetch only filled Prescription Items
	this.getAllFilledPrescriptionDrugItems = function getAllFilledPrescriptionDrugItems() {
		var aAllPrescriptionItems = [];
		for (var iii = 0; iii < this.aPrescriptionItems.length; ++iii) {
			if (this.aPrescriptionItems[iii] != null && this.aPrescriptionItems[iii].aDrugItems.length > 0) {
				aAllPrescriptionItems.push(this.aPrescriptionItems[iii].getPrescriptionDrugItemDetails());
			}
		}
		return aAllPrescriptionItems;
	}

	// fetches details of a single prescription 
	this.getPrescriptionDrugItem = function getPrescriptionDrugItem(iIndex){
		if(this.aPrescriptionItems[iIndex]!=null){
			return this.aPrescriptionItems[iIndex].getPrescriptionDrugItemDetails();
		}else{
			return false;
		}
	}

	// fetches a single prescription object
	this.getPrescriptionDrugItemObject = function getPrescriptionDrugItemObject(iIndex){
		if(this.aPrescriptionItems[iIndex]!=null){
			return this.aPrescriptionItems[iIndex];
		}else{
			return false;
		}
	}

	// fetches no of  prescription Items
	this.getPrescriptionItemsCount = function getPrescriptionItemsCount(){
		var iCount = 0;
		for(var iii=0; iii < this.aPrescriptionItems.length; ++iii){
			if(this.aPrescriptionItems[iii]!=null){
				if(this.aPrescriptionItems[iii].bIsValid){
					++iCount;				
				}
			}
		}
		return iCount;
	}

	// set formulations for drug
	// aDrugROA in format {"key":"value"}
	this.setPrescriptionDrugROAs = function setPrescriptionDrugROAs(aDrugROA){
		for(var iii=0; iii < aDrugROA.length; ++iii){
			aPrescriptionDrugROA.push(aDrugROA[iii]);
		}
	}

	//set drug formulation
	this.setPrescriptionDrugROA = function setPrescriptionDrugROA(iIndex,iROA){
		this.aPrescriptionItems[iIndex].setROA(iROA);
	} 

	// set current patient medications
	this.setPatientCurrentMedications = function setPatientCurrentMedications(aCurentMedication){
		aPrescriptionCurrentMedication.push(aCurentMedication);
	}

	this.setAllowDrugsToBeAdded = function(allowNewDrugsToBeAdded = false){
		this.allowNewDrugsToBeAdded = allowNewDrugsToBeAdded;
	}

	this.setAllowNewDrugsToBeCreated = function(allowNewDrugsToBeCreated = false){
		this.allowNewDrugsToBeCreated = allowNewDrugsToBeCreated;
	}
}

// represents a single prescription. A single prescription can have multiple drugs of same name but varying strength
function PrescriptionDrugItem(){
	this.aDrugItems = [];
	this.dStartDate ="";
	this.dEndDate = "";
	this.iNoOfDays = 0;
	this.iNoOfTablets = 0;
	this.iDose = 0;
	this.iDrugSelected=0; // represents the drug selected in aDrugItems
	this.bIsValid = false; // represents whether the prescription item is valid or not
	this.iIsStopped = 0;  // 0 means not stopped; 1 means stopped
	this.oStoppingReason={'sReason':'','sStoppedBy':'','dStoppedAt':''}; // stopping reason
	this.aInteractionDrugs = [];
	this.bIsNewDrug = false; // 1: drug is not in system and will be added. 0: drug is present in the system
	
	this.addDrug = function addDrug(oDrug){
		this.aDrugItems.push(oDrug);
	}

	//set formulation
	this.setFormulation = function setFormulation(sFormulation){
		this.aDrugItems[this.iDrugSelected].setFormulation(sFormulation);
	}
	
	//  select drug when strength is changed
	this.setSelectedDrug = function setSelectedDrug(iDrugSelected){
		this.iDrugSelected=iDrugSelected;
	}

	// set drug dosage
	this.setDrugDosage = function setDrugDosage(sDosage){
		this.aDrugItems[this.iDrugSelected].setDrugDosage(sDosage);
	}
	
	//set start date
	this.setStartDate = function setStartDate(dStartDate){
		this.dStartDate =dStartDate;

		// If no of days is there but not end date
		if (!this.dEndDate && this.iNoOfDays){
			var dDate = this.dStartDate;
			var sTempDate = dDate.split('-');

			var dDate = moment([sTempDate[1] + '-' + sTempDate[0] + '-' + sTempDate[2]], "MM-DD-YYYY");
			dDate = dDate.add(this.iNoOfDays - 1, 'days');

			this.dEndDate = dDate.format("DD-MM-YYYY");
		}

		this.setNoOfDays();
	}	

	//set end date
	this.setEndDate = function setEndDate(dEndDate){
		this.dEndDate =dEndDate;
		
		// If no of days is there but not start date
		if (!this.dStartDate && this.iNoOfDays) {
			var dDate = this.dEndDate;
			var sTempDate = dDate.split('-');

			var dDate = moment([sTempDate[1] + '-' + sTempDate[0] + '-' + sTempDate[2]], "MM-DD-YYYY");
			dDate = dDate.subtract(this.iNoOfDays - 1, 'days');

			this.dStartDate = dDate.format("DD-MM-YYYY");
		}

		this.setNoOfDays(); 
	}

	//set no of days based on start and end date
 	this.setNoOfDays = function setNoOfDays(iCurrentNoOfDays){
 		var dFromDate = this.dStartDate;
 		var dToDate = this.dEndDate;
 		var sTemp = dFromDate.split('-');
 		var tTemp = dToDate.split('-');
 
 		var dEndDate = moment([tTemp[1]+'-'+tTemp[0]+'-'+tTemp[2]],"MM-DD-YYYY");
 		var dStartDate = moment([sTemp[1]+'-'+sTemp[0]+'-'+sTemp[2]],"MM-DD-YYYY");
 		var iNoOfDays = dEndDate.diff(dStartDate, 'days')+1;
 		 
 		if(iNoOfDays >= 0){
 		    this.iNoOfDays = iNoOfDays;
		} else if (iCurrentNoOfDays) {
			this.iNoOfDays = iCurrentNoOfDays;
		} else {
			this.iNoOfDays = 0;
		} 		
 	}

	//set number of days manually
	this.setNumberOfDaysManually = function setNumberOfDaysManually(iNoOfDays){
		this.iNoOfDays = iNoOfDays;
		
	}

	// to set no of tablets 
	this.setNoOfTablets = function setNoOfTablets(iNoOfTablets=0){
		this.iNoOfTablets = iNoOfTablets
	}

	// set drug instructions 
	this.setDrugInstructions = function setDrugInstructions(sInstructions){
		this.aDrugItems[this.iDrugSelected].setDrugInstructions(sInstructions);
	}

	//set dose
	this.setDose = function setDose(iDose){
		this.iDose = iDose;
	}

	//set roa
	this.setROA = function setROA(iROA){
		this.aDrugItems[this.iDrugSelected].setROA(iROA);
	}

	//set roa
	this.setROAName = function setROAName(sROA){
		this.aDrugItems[this.iDrugSelected].setROAName(sROA);
	}

	this.setInteractionDrugs = function(aInteractionDrugs){
		this.aDrugItems[this.iDrugSelected].setInteractionDrugs(aInteractionDrugs);
	}

	this.setStrength = function (sStrength) {
		this.aDrugItems[this.iDrugSelected].setStrength(sStrength);
	}

	// return prescription details
	this.getPrescriptionDrugItemDetails = function getPrescriptionDrugItemDetails(){
		var oThisPrescriptionDrugItem = {
			"iDrugId":this.aDrugItems[this.iDrugSelected].getDrugId(),
			"sGenericName":this.aDrugItems[this.iDrugSelected].getGenericName(),
			"sDrugName":this.aDrugItems[this.iDrugSelected].getDrugName(),
			"sFormulation":this.aDrugItems[this.iDrugSelected].getFormulation(),
			"sStrength":this.aDrugItems[this.iDrugSelected].getDrugStrength(),
			"sDosage":this.aDrugItems[this.iDrugSelected].getDrugDosage(),
			"dStartDate":this.dStartDate,
			"dEndDate":this.dEndDate,
			"iNoOfDays":this.iNoOfDays,
			"iNoOfTablets":this.iNoOfTablets,
			"sInstructions":this.aDrugItems[this.iDrugSelected].getDrugInstructions(),
			"iDose":this.iDose,
			"sInteractions":this.aDrugItems[this.iDrugSelected].getDrugIneractions(),
			"sContraIndications":this.aDrugItems[this.iDrugSelected].getDrugContraIndications(),
			"sSideEffects":this.aDrugItems[this.iDrugSelected].getDrugSideEffects(),
			"sAvailablity":this.aDrugItems[this.iDrugSelected].getDrugAvailability(),
			"iIsStopped":this.getIsStopped(),
			"sROA":this.aDrugItems[this.iDrugSelected].getROAName(),
			"iROA":this.aDrugItems[this.iDrugSelected].getROA(),
			"aInteractionDrugs": this.aDrugItems[this.iDrugSelected].getInteractionDrugs(),
			"sName": this.aDrugItems[this.iDrugSelected].getName(),
			"bIsNewDrug": this.bIsNewDrug,
		};

		return oThisPrescriptionDrugItem;
	}

	// set drug availability 
	this.setDrugAvailability = function setDrugAvailability(sAvailablity){
		this.aDrugItems[this.iDrugSelected].setDrugAvailability(sAvailablity);
	}

	// get drug availability 
	this.getDrugAvailability = function getDrugAvailability(){
		this.aDrugItems[this.iDrugSelected].getDrugAvailaiblity();
	}

	// to get selected drug's id
	this.getSelectedDrugId = function getSelectedDrugId(){
		return this.aDrugItems[this.iDrugSelected].getDrugId();
	}

	// to get strength of every drug in a prescription
	this.getAllDrugStrength = function getAllDrugStrength(){
		var aStrengths=[];
		for(var iii=0 ; iii< this.aDrugItems.length; ++iii){
			var oStrength = {
				"id":this.aDrugItems[iii].getDrugId(),
				"strength":this.aDrugItems[iii].getDrugStrength()
			}
			aStrengths.push(oStrength);
		}
		return aStrengths;
	}

	// to validate prescription item
	this.validatePrescriptionItem = function validatePrescriptionItem(){
		this.bIsValid = true;
	}

	// to invalidate prescription item
	this.invalidatePrescriptionItem = function invalidatePrescriptionItem(){
		this.bIsValid = false;
	}

	// set isStopped
	this.setIsStopped = function setIsStopped(iIsStopped){
		this.iIsStopped = iIsStopped;
	}

	// get isStopped
	this.getIsStopped = function getIsStopped(){
		return this.iIsStopped;
	}

	this.setStoppingReason = function(sReason='',sStoppedBy='',dStoppedAt='') 
	{
		if (sReason == undefined || sReason == null || sReason == '') {
			sReason == 'NA';
		}
		this.oStoppingReason = {'sReason':sReason,'sStoppedBy':sStoppedBy,'dStoppedAt':dStoppedAt};
	}

	this.getStoppingReason = function()
	{
		return this.oStoppingReason;
	}
}

// create Drug object 
function Drug(aDrugDetails){
	
	this.iDrugId			= aDrugDetails['drug_id'];
	this.sDrugCode			= aDrugDetails['drug_code'];
	this.sDrugName			= aDrugDetails['drug_name'];
	this.sGenericName		= aDrugDetails['generic_name'];
	this.sFormulation		= aDrugDetails['formulation'];
	this.sStrength			= aDrugDetails['strength'];
	this.sDosage			= aDrugDetails['dosage'];
	this.sDrugFor			= aDrugDetails['drug_for'];
	this.sInstructions      = aDrugDetails['instructions'];
	this.sInteractions      = aDrugDetails['drug_interaction'];
	this.sContraIndications = aDrugDetails['contra_indication'];
	this.sSideEffects		= aDrugDetails['side_effects'];
	this.sCategory			= aDrugDetails['category'];
	this.sManufacturer		= aDrugDetails['manufacturer'];
	this.sProductCost		= aDrugDetails['product_cost'];
	this.sProductPrice		= aDrugDetails['product_price'];
	this.iROA				= aDrugDetails['route_of_administration_id'];
	this.sROA				= aDrugDetails['route_of_administration'];
	this.sSpecialization	= aDrugDetails['specialisation'];
	this.sAvailablity		= "";
	this.aInteractionDrugs = aDrugDetails['aInteractionDrugs'] ? aDrugDetails['aInteractionDrugs'] : [];
	//get drug Id
	this.getDrugId = function getDrugId(){
		return this.iDrugId;
	}

	// get generic name 
	this.getGenericName = function getGenericName(){
		return this.sGenericName;
	}

	// get drug name
	this.getDrugName = function getDrugName(){
		return this.sDrugName;
	}
	this.getName = function getName(){
		return this.sGenericName + " (" + this.sDrugName + ")" + "[" + [this.sStrength, this.sFormulation].join('-') +"]";
	};


	// set drug formulation
	this.setFormulation = function setFormulation(sFormulation){
		this.sFormulation = sFormulation;
	}
	// get Drug formulations
	this.getFormulation = function getFormulation(){
		return this.sFormulation;
	}
	//get drug strength
	this.getDrugStrength = function getDrugStrength(){
		return this.sStrength;
	}
	//set drug dosage
	this.setDrugDosage = function setDrugDosage(sDosage){
		this.sDosage = sDosage;
	}
	//get drug dosage
	this.getDrugDosage = function getDrugDosage(){
		return this.sDosage;
	}
	// set drug instructions
	this.setDrugInstructions = function setDrugInstructions(sInstructions){
		this.sInstructions = sInstructions;
	}
	// get drug instructions
	this.getDrugInstructions = function getDrugInstructions(){
		return this.sInstructions;
	}
	// to get drug interactions
	this.getDrugIneractions = function getDrugIneractions(){
		return this.sInteractions;
	}
	// to get drug contra Indications
	this.getDrugContraIndications = function getDrugContraIndications(){
		return this.sContraIndications;
	}
	// to get drug side effects
	this.getDrugSideEffects = function getDrugSideEffects(){
		return this.sSideEffects;
	}
	//set drug availability
	this.setDrugAvailability = function setDrugAvailability(sAvailablity){
		// set drug availabililty
		this.sAvailablity= sAvailablity;
	}

	this.setInteractionDrugs = function setInteractionDrugs(aInteractionDrugs){
		this.aInteractionDrugs = aInteractionDrugs;
	}

	//get drug availability
	this.getDrugAvailability = function getDrugAvailability(){
		return this.sAvailablity;
	}
	// set drug roa
	this.setROA = function setROA(iROA){
		this.iROA = iROA;
	}
	// get Drug roa
	this.getROA = function getROA(){
		return this.iROA;
	}

	this.getInteractionDrugs = function getInteractionDrugs(){
		return this.aInteractionDrugs;
	};

	// set drug roa
	this.setROAName = function setROAName(sROA){
		this.sROA = sROA;
	}
	// get Drug roa
	this.getROAName = function getROAName(){
		return this.sROA;
	}

	this.setStrength = function (sStrength) {
		this.sStrength = sStrength;
	}
}