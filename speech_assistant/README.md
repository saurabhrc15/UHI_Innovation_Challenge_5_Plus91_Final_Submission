# About AugnitoSDK

AugnitoSDK is a javascript library for consuming Augnito speechApi .  

It contains sample code for HTML form , CK editor and Command and Control testing.  

## HTML Form:
HTML form is a basic form, where you can dictate using the speech api and also navigate with speech.  
This is an demo for how you can naviage in your EMR.   

Code present in :  
    ./JsSDK/DemoJS/HtmlFormLogic.js  
    ./Index.html  
    ./lib/HtmlFormEditorProcess.js  

### HTML form page command and control

Cursor navigation within forms

> * Go to patientId
> * Go to Date of birth
> * Go to Diagnosis
> * Go to Medication
> * Go to History
> * Go to next filed 
> * Go to previous filed  


### Editing command inside html text box.

> * delete it/select it/delete last word/select last word
> * {delete|select} {last|next} {word|line|sentence}
> * {delete|select} {last|next} {n} {word|line|sentence}
> * go to line start 
> * go to line end
> * go to document start
> * go to document end
> * select last/next n word
> * give space


## CK Editor:  
This is a demo for dictation in CK editor.   

Code present in :  
    ./JsSDK/DemoJS/CKEditoLogic.js  
    ./CkEditorDemo.html  
    ./lib/CKEditorProcess.js  

### Few sample command which work on ckEdiotr.  

> * Delete last n words 
> * Select last n words
> * Bold it
> * Italic it
> * Delete it
> * Bold last n words
> * Italic last n words
> * Start/Stop bullet list
> * Start/Stop number list

## Froala Editor:  
This is a demo for dictation in Froala editor.   

Code present in :  
    ./JsSDK/DemoJS/FroalaEditorLogic.js  
    ./FroalaDemo.html  
    ./lib/FroalaEditorCommandProcess.js  

### Few sample command which work on Froala Ediotr.  

> * Delete last n words 
> * Select last n words
> * Bold it
> * Italic it
> * Delete it
> * Bold last n words
> * Italic last n words


# Following is sample code to initiate SDK. 

> They Contain all the callback your app needs to implement to use the sdk.  

```javascript

    var appLogic = {};
    appLogic.HyperTextControl = $("#hyperText")
    appLogic.EnableLogs = false;
    appLogic.OnChangeMicState = function(isConnected){} ;
    appLogic.DynamicCommand = function (ActionRecipe) {};
    appLogic.FinalResultCallback = function (ActionRecipe) {};
    appLogic.OnSessionEvent = function (meta) {}
    appLogic.onPartialResults = function (response) { }
    appLogic.onFinalResults = function (response) {}
    appLogic.onReadyForSpeech = function () {},
    appLogic.onEndOfSession = function () {},
    appLogic.onError = function (code, data) {},
    appLogic.onEvent = function (eventCode, data) { }
    var augnitoClient = GetAugnitoClient(appLogic);
    $("#btnAugnitoMic").click(function () {
        augnitoClient.toggleListening();
    });

```

> This Function is used to add default attributes to your app which are needed by the sdk.  

```javascript 

var GetAugnitoClient = function (appLogic) {
    
    appLogic.Server= "wss://apis.textflow.pro/speechapi";
    appLogic.ContentType= "audio/x-raw,+layout=(string)interleaved,+rate=(int)16000,+format=(string)S16LE,+channels=(int)1";
    appLogic.AccessKey= "57c12rfg79c0403328aaed3ab041341234";
    appLogic.AccountCode= "812d66e4-5233ds-49c2-9270-1rr124d";
    appLogic.NoiseCt= "-1";
    appLogic.LmId= 38;
    appLogic.UserTag="usercode";
    appLogic.LoginToken="LoginToken";
    appLogic.OtherInfo="otherinfo";
    appLogic.SourceApp="SDKdemo";      
    return new AugnitoSDK(appLogic);    
}

```

### SDK Debug event.

> *  WS_CONNECTING = 1;
> * MSG_MEDIA_STREAM_CREATED = 2;
> * MSG_INIT_RECORDER = 3;
> * MSG_RECORDING = 4;
> * MSG_SEND_EMPTY = 6;
> * MSG_WEB_SOCKET_OPEN = 9;
> * MSG_WEB_SOCKET_CLOSE = 10;
> * MSG_STOP = 11;

### Public functions from SDK.

> * startListening
> * toggleListening
> * stopListening


### General command 

> * Stop mic


## Action Recipe

Action recipe is java script object which will be passed through a chain of controls.  It's has all the information that are needed to perform static, dyanicm commands or typing in editor.

bellow are some of the fields  
var ActionRecipe = new Object()  
   
> * ActionRecipe.Name: This is normalize form of speech output. after removing all space in between word. ex. liver is normal become 'liverisnormal'
> * ActionRecipe.SessionCode : unique session code
> * ActionRecipe.Final: Indicate whether speech output is final or partial.
> * ActionRecipe.IsCommand: Indicate whether speech output is marked command. either from server or app processing.
> * ActionRecipe.ReceivedText: This is Speech out put without any local processing to type in editor.
   
   
        