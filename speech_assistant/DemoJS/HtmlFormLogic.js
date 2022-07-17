var augnitoClient;
$(document).ready(function () {

    // List of HTML control for navigation commands
    var ListOfControls = [
        { "ControlId": "txtPid", "ControlName": "patientid" },
        { "ControlId": "txtDOB", "ControlName": "dateofbirth" },
        { "ControlId": "txtSpecimen", "ControlName": "specimen" },
        { "ControlId": "txtGross", "ControlName": "gross" },
        { "ControlId": "txtMicroscopic", "ControlName": "microscopic" },
        { "ControlId": "txtDiag", "ControlName": "diagnosis" },
        { "ControlId": "txtMed", "ControlName": "medication" },
        { "ControlId": "txtaBody", "ControlName": "history" }
    ];

    $("#txtPid").focus();
    var appLogicHtmlForm = {};
    appLogicHtmlForm.HyperTextControl = $("#FloatingHyperText")
    appLogicHtmlForm.EnableLogs = false;
    appLogicHtmlForm.ApplicationControls = ListOfControls;

    appLogicHtmlForm.OnChangeMicState = function (isConnected) {
        
        //This will be called when mic recording start OR stop to update UI interface mic button.
        if (isConnected) {
            appLogicHtmlForm.HyperTextControl.html("");
            $("#btnAugnitoMic").addClass("AugnitoStartButtonStyle");
        }
        else {
            $("#btnAugnitoMic").removeClass("AugnitoStartButtonStyle");
        }
    }
    appLogicHtmlForm.InterfaceCommand=function(ActionRecipe)
    {
        // Any UI Command to be process here, before it reaches the editor
        if (AugnitoCMDs.STOP_MIC == ActionRecipe.Name) {
            augnitoClientHtmlForm.stopListening();
            return true;
        }
        return false;
    }
    appLogicHtmlForm.TrimLeft=function(ReceivedText)
    {
        //Received trim left , Trim left also remove new line, so preserver it
        ReceivedText = ReceivedText.replace(/\n/gi, "@newline@");
        ReceivedText = ReceivedText.trimLeft();
        ReceivedText = ReceivedText.replace(new RegExp("@newline@", "gi"), "\n");
        return ReceivedText;   
    }
    appLogicHtmlForm.HandleEditorCursorContext = function (receivedText, caretPosition, currentText) {
        // We called this part of code, client side beatification. 
        // In client side beatification text need to be processed for better formatting,
        // The Processing done here is :- 
        //      Make first Charecter capital, if after fullstop.
        //      Remove prefix spaces as and when needd.                

        var editorReady = receivedText;
        if (currentText == "") {
            // Is Empty than remove pre space and make first char capital
            editorReady = appLogicHtmlForm.TrimLeft(editorReady);
            editorReady = editorReady[0].toUpperCase() + editorReady.substring(1);
        }
        // Remove prfix spaces for newline and new paragraph
        if (editorReady == " \n" || editorReady == " \n\n")
        {
            // Avoid tail space on above line.
            editorReady = appLogicHtmlForm.TrimLeft(editorReady);
        }

        if (currentText.length > 1 && currentText[caretPosition - 1] == " " && currentText[caretPosition - 2] == ".") {
            // Capital after full stop
            editorReady = appLogicHtmlForm.TrimLeft(editorReady);
            editorReady = editorReady[0].toUpperCase() + editorReady.substring(1);
        }

        if (currentText.length > 0 && currentText[caretPosition - 1] == " ") {
            // If already segment has space then remove server given space, as the user has moved the cursor. 
            editorReady = appLogicHtmlForm.TrimLeft(editorReady);
        }

        if (currentText.length > 0 && currentText[caretPosition - 1] == "\n") {
                // Start of new line
                editorReady = appLogicHtmlForm.TrimLeft(editorReady);
                editorReady = editorReady[0].toUpperCase() + editorReady.substring(1);            
        } 
        
        if(currentText.length > 0 && (currentText[caretPosition - 1] == "." || currentText[caretPosition - 1] == ":"))
         {
            // Capital after full stop
            var trimleftText = appLogicHtmlForm.TrimLeft(editorReady);
            if(trimleftText.length>0)
            {
                editorReady = " " + trimleftText[0].toUpperCase();
            }
            if(trimleftText.length>1)
            {
                editorReady =editorReady+trimleftText.substring(1);    
            }
        }
        if (editorReady.trim() == "." || editorReady.trim() == ":") {
            editorReady = appLogicHtmlForm.TrimLeft(editorReady);
        }
        
        return editorReady;
    }
    appLogicHtmlForm.CommandAndControlCallback = function (ActionRecipe) {

        if(appLogicHtmlForm.InterfaceCommand(ActionRecipe))
        {
           return;
        }
        HtmlFormEditorProcess.ProcessCommand(ActionRecipe, appLogicHtmlForm.ApplicationControls);        
    };

    appLogicHtmlForm.FinalResultCallback = function (ActionRecipe) {

        var activeDocumentElement = $(document.activeElement);
        var caretPosition = activeDocumentElement.prop("selectionStart");
        var currentText = activeDocumentElement.val();
        var editorReady = appLogicHtmlForm.HandleEditorCursorContext(ActionRecipe.ReceivedText, caretPosition, currentText);
        HtmlFormEditorProcess.StringWriteAtCaret(currentText, editorReady, caretPosition);

        // Handle auto scroll if needed.
        if (document.activeElement.type == "textarea") {
            var lineHeight = parseInt(activeDocumentElement.css('line-height'));
            var lineNumber = document.activeElement.value.substr(0, document.activeElement.selectionStart).split("\n").length;
            activeDocumentElement.scrollTop(lineNumber * lineHeight);
        }
        else if (document.activeElement.type == "text") {
            var currentIndex = activeDocumentElement.prop("selectionStart");
            activeDocumentElement.blur();
            activeDocumentElement.focus();
            activeDocumentElement.prop("selectionStart", currentIndex);
            activeDocumentElement.prop("selectionEnd", currentIndex);
        }
    };
    appLogicHtmlForm.OnSessionEvent = function (meta) {
         
       // Session event will be called during speech session(between mic start and stop) 
       // whenever server has to send information to client app which is not relaed to ASR output.
        var sessionMetaData = meta;
        if (appLogicHtmlForm.EnableLogs) {
            console.log(sessionMetaData);
        }
        var event = sessionMetaData.Event;
        if (event == 'None') {
            // Error case
            return;
        }

        var eventType = sessionMetaData.Event.Type;
        var eventValue = sessionMetaData.Event.Value;
        if (eventType == "SESSION_CREATED") {
            var Sessiontoken = eventValue;
            localStorage.setItem("WebSocketConnectionStatus", "On");
            console.log("session Token "+ Sessiontoken);
            // After successful authenticate, server creates an unique ID for each speech session and sends it back to client app for reference.
            // Client app can store this is it requires.            
        }
        else if (eventType == "SERVICE_DOWN") {
            // Very rare, But This event will come when Speech server's any internal component down.
            console.log(eventType);
        }
        else if (eventType == "NO_DICTATION_STOP_MIC") {
           // Some time user start mic and forgot after it. start doing discussion with colleague or on phone.
           // In this case mic is on and user is not dictating any valid speech for trascription. Server can detect such situations and send an event to confirm from user.
            HandleMicOff();
        }
        else if (eventType == "INVALID_AUTH_CREDENTIALS") {
            // This event happens when one of following is invalid.
            // AccountCode, AccessKey, Active subscription for trial or paid. lmid.
            console.log(eventType);
            $("#MessageDialog").dialog("open");
        }
        else if ("LOW_BANDWIDTH" == eventType) {
             // Speech API need continues upload speed of 32KBps if it raw audio data with 16k sampling rate.
             // If fluctuation in internet than speech output may be delayed. It's good to notify that speech may delayed due to poor network connection.
             // Client app can use this event to show un attendant popup to indicate network status. 
             console.log(eventType);
        }
    }
    appLogicHtmlForm.onPartialResults = function (response) {

        // Partial output against audio stream started to server. This is not final and keep changing.
        // Use this to show user that system started listing, and Processing your voice.
        var partialText = response.Result.Transcript;
        if (partialText && partialText.length > 52) {
            partialText = '..' + partialText.substring(partialText.length - 50);
        }
        appLogicHtmlForm.HyperTextControl.html(partialText);
    }
    appLogicHtmlForm.onFinalResults = function (response) {

        // Prepare Action Recipe from speech output to pass in editor handle.
        // When System make output final this even will be call.
        // It can be either static command, or normal transcription. 
        appLogicHtmlForm.HyperTextControl.html('');
        var ActionRecipe = new Object();
        var text=response.Result.Transcript;
        var Action=response.Result.Action;

        ActionRecipe.Name=text.replace(/\s+/g,'');
        ActionRecipe.SessionCode=response.SessionCode;
        ActionRecipe.Final=response.Result.Final;
        ActionRecipe.IsCommand=response.Result.IsCommand;
        if(ActionRecipe.IsCommand)
        {
            ActionRecipe.Action=Action.replace(/\s+/g,'');
        }
        ActionRecipe.ReceivedText=text;
        webWorkerRichEdit.postMessage(ActionRecipe);
    }
    appLogicHtmlForm.onReadyForSpeech = function () {
        // On socket connection established 
        this.OnChangeMicState(true);
    },
    
    appLogicHtmlForm.onEndOfSession = function () {
        // On mic off and connection close
        this.OnChangeMicState(false);
    },
    appLogicHtmlForm.onError = function (code, data) {
        if (appLogicHtmlForm.EnableLogs) {
            console.log("ERR: " + code + ": " + (data || ''));
        }
    },
    appLogicHtmlForm.onEvent = function (eventCode, data) {
      
        // All these events are from client side SDK only for debugging purpose.
        // Speech server will not be calling them
        switch(eventCode) {

            case AugnitoSDKEvent.WS_CONNECTING:
                console.log("WS_CONNECTING");
                appLogicHtmlForm.HyperTextControl.html(data);
              break;
            case AugnitoSDKEvent.MSG_MEDIA_STREAM_CREATED:
                 //console.log("MSG_MEDIA_STREAM_CREATED");
                // code block
               break;
            case AugnitoSDKEvent.MSG_INIT_RECORDER:
                // console.log("MSG_INIT_RECORDER");              
              break;
            case AugnitoSDKEvent.MSG_RECORDING:
                //console.log("MSG_RECORDING");              
              break;
            case AugnitoSDKEvent.MSG_SEND_EMPTY:
                //console.log("MSG_SEND_EMPTY");              
              break;
            case AugnitoSDKEvent.MSG_WEB_SOCKET_OPEN:
              //  console.log("MSG_WEB_SOCKET_OPEN");              
              break;
            case AugnitoSDKEvent.MSG_WEB_SOCKET_CLOSE:
                //console.log("MSG_WEB_SOCKET_CLOSE");
                appLogicHtmlForm.HyperTextControl.html("");
              break;
            case AugnitoSDKEvent.MSG_STOP:
                //console.log("MSG_STOP");              
              break;
            default:
               // console.log("default");              
          }
    },
    appLogicHtmlForm.performMicOnAction = function () {
        try {
            $("#DuplicateMicWarningBox-dialog").dialog("close");
            $('#DuplicateMicWarningBox-dialog').remove();

            $('<div id="DuplicateMicWarningBox-dialog">' +
                '<p id="timeout-message1">Augnito mic is ON in another tab. Click "Use Here" to dictate in this tab.</p>' +
                '</div>')
                .appendTo('body')
                .dialog({
                    modal: true,
                    width: '351px',
                    minHeight: 'auto',
                    zIndex: 1000000000,
                    closeOnEscape: true,
                    draggable: false,
                    resizable: false,
                    closeText: '',
                    close: function () {
                        $('#DuplicateMicWarningBox-dialog').remove();
                    },
                    dialogClass: 'messageBox-dialog',
                    title: 'Augnito Mic',
                    buttons: {
                        'Confirm-button': {
                            text: 'Use Here',
                            id: "contunue-template-btn",
                            class: 'btnMacroButton btnMacroButtonAdd',
                            click: function () {
                                $(this).dialog("close");
                                $('#DuplicateMicWarningBox-dialog').remove();

                                var currentMicStatus = localStorage.getItem('WebSocketConnectionStatus');
                                if (currentMicStatus == "Off") {
                                    augnitoClientHtmlForm.startListening();
                                }
                                else {
                                    requiredMicRestart = true;
                                    localStorage.setItem('WebSocketConnectionStatus', 'RequestOff');
                                }

                            }
                        },
                        'cancel-button': {
                            text: 'Close',
                            class: 'btnMacroButton btnMacroCancel',
                            id: "cancel-template-button",
                            click: function () {
                                $(this).dialog("close");
                                $('#DuplicateMicWarningBox-dialog').remove();
                                augnitoClientHtmlForm.getConfig().onEvent(AugnitoSDKEvent.MSG_WEB_SOCKET_CLOSE, '');
                            }
                        }
                    }
                });
        } catch (e) {

        }
    }
    augnitoClientHtmlForm = GetAugnitoClient(appLogicHtmlForm);
    $("#btnAugnitoMic").click(function () {
        augnitoClientHtmlForm.toggleListening();
    });

    var QRCodeData = appLogicHtmlForm.AccountCode + "|" + appLogicHtmlForm.AccessKey + "|" + appLogicHtmlForm.UserTag + "|" + DeviceId + "|" + appLogicHtmlForm.LmId + "|" + appLogicHtmlForm.SourceApp + "|" + appLogicHtmlForm.DomainName;
    new QRCode(document.getElementById("qrcode"), QRCodeData);

    createMobileWebSocket();

    function processRichEditInWebWorker(_function) {
        // Create worker for background processing.  Client app may not need this if they are not processing final text much.
        // But better to keep this, to de-couple UI thread and background thread.

        var workerURL = URL.createObjectURL(new Blob([_function.toString(),
        ';this.onmessage =  function (eee) {' + _function.name + '(eee.data);}'
        ], {
                type: 'application/javascript'
            }));

        var worker = new Worker(workerURL);
        worker.workerURL = workerURL;
        return worker;
    }

    function ProcessResponseData(ActionRecipe) {
        // complex processing if any
        self.postMessage(ActionRecipe);
    }

    var webWorkerRichEdit = processRichEditInWebWorker(ProcessResponseData);
    webWorkerRichEdit.onmessage = function (event) {

        var ActionRecipe=event.data        
        if (ActionRecipe.IsCommand) {
            // Static commands , server will give action name
            ActionRecipe=AugnitoCMDStatic.PrepareRecipe(ActionRecipe);
            appLogicHtmlForm.CommandAndControlCallback(ActionRecipe);
        }
        else {

            // Look for dynamic commands
            ActionRecipe=AugnitoCMDRegex.PrepareRecipe(ActionRecipe);      
            if (ActionRecipe.IsCommand) {
                appLogicHtmlForm.CommandAndControlCallback(ActionRecipe);                
            }
            else
            {
                appLogicHtmlForm.FinalResultCallback(ActionRecipe);
            }
        }
        
    };

    window.addEventListener("beforeunload", function (e) {
        augnitoClientHtmlForm.stopListening();
    });
});



