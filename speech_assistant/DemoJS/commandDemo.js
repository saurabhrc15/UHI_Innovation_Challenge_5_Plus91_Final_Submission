$(document).ready(function () {

   
    var appLogic = {};
    appLogic.HyperTextControl = $("#FloatingHyperText")
    appLogic.EnableLogs = false;
    appLogic.OnChangeMicState = function (isConnected) {
        if (isConnected) {
            appLogic.HyperTextControl.html("");
            $("#btnAugnitoMic").addClass("AugnitoStartButtonStyle");
            $("#dvspeechOutupt").removeClass("classDetect");
            $("#cmdOutput").removeClass("classDetect");      
        }
        else {
            $("#btnAugnitoMic").removeClass("AugnitoStartButtonStyle");
            $("#dvspeechOutupt").removeClass("classDetect");
            $("#cmdOutput").removeClass("classDetect");      
        }
    }
    appLogic.OnSessionEvent = function (meta) {
        var sesstionMetaData = meta;
        if (appLogic.EnableLogs) {
            console.log(sesstionMetaData);
        }
    }
    appLogic.CommandAndControlCallback = function (recipe) {

       var text=recipe.ReceivedText.replace('\n','\\n');
       $("#dvspeechform").text(text);
       $("#dvaction").text(recipe.Action);   
      
       $("#dvspeechOutupt").removeClass("classDetect");
       $("#cmdOutput").addClass("classDetect");      
       $("#cmdhistory").append("<span>"+recipe.ReceivedText+"</span> 	&rarr; <span>"+recipe.Action+"</span><br>");           
       
    };
    appLogic.DynamicCommandORASROutput = function (recipe) {
        
        $("#asroutput").text(recipe.ReceivedText);
        $("#dvspeechOutupt").addClass("classDetect");
        $("#cmdOutput").removeClass("classDetect");
        $("#typehistory").append("<span>"+recipe.ReceivedText+"</span><br>");            
    };
     appLogic.onPartialResults = function (response) {
        var partialText = response.Result.Transcript;
        if (partialText && partialText.length > 82) {
            partialText = '..' + partialText.substring(partialText.length - 80);
        }
        appLogic.HyperTextControl.html(partialText);
    }
    appLogic.onFinalResults = function (response) {
        appLogic.HyperTextControl.html('');
        var ActionRecipe = new Object();
        var text = response.Result.Transcript;
        ActionRecipe.Name = text.replace(/\s+/g, '');
        ActionRecipe.Action = response.Result.Action;
        ActionRecipe.SessionCode = response.SessionCode;
        ActionRecipe.Final = response.Result.Final;
        ActionRecipe.IsCommand = response.Result.IsCommand;
        ActionRecipe.ReceivedText = text;
        if (ActionRecipe.IsCommand) {
            appLogic.CommandAndControlCallback(ActionRecipe);
        }
        else
        {
            appLogic.DynamicCommandORASROutput(ActionRecipe);
        }
    }
    appLogic.onReadyForSpeech = function () {
        this.OnChangeMicState(true);
    },
    appLogic.onEndOfSpeech = function () {
        this.OnChangeMicState(false);
    },
    appLogic.onEndOfSession = function () {
        this.OnChangeMicState(false);
    }
    appLogic.onEvent = function (eventCode, data) {
        if (eventCode == 1) {
            appLogic.HyperTextControl.html(data);
        }
        if (eventCode == 10) {
            appLogic.HyperTextControl.html("");
        }
    }
    var augnitoClient = GetAugnitoClient(appLogic);
    $("#btnAugnitoMic").click(function () {
        augnitoClient.toggleListening();
    });

  

});



