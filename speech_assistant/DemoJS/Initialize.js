var GetAugnitoClient = function (appLogic) {

    if (typeof URLSearchParams != "undefined") {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('UserTag')) {
            appLogic.UserTag = urlParams.get('UserTag');
        }
    }

    appLogic.DomainName = "apis.augnito.ai";

    appLogic.PushNotification = "wss://" + appLogic.DomainName + "/speechapi/notification/";
    appLogic.SpeechMicURL = "wss://" + appLogic.DomainName + "/speechapi/mobile/client/";

    appLogic.MacroServiceURL = "https://" + appLogic.DomainName + "/manage/";

    appLogic.Server = "wss://" + appLogic.DomainName + "/speechapi";
    appLogic.AccountCode = "a0e79b18-13b3-479c-850f-3121144c516f";
    appLogic.AccessKey = "392b6c01e43d47de9e4d054660c7afce";

    appLogic.ContentType = "audio/x-raw,+layout=(string)interleaved,+rate=(int)16000,+format=(string)S16LE,+channels=(int)1";
    appLogic.NoiseCt = "0.005";
    appLogic.LmId = 101; /*Change LmId*/
    if (typeof appLogic.UserTag == "undefined") {
        appLogic.UserTag = "userInfo"; /*user name or user unique information*/
    }
    appLogic.LoginToken = "userLoginToken"; /*Login token or unique login id*/
    appLogic.OtherInfo = "otherinfo";
    appLogic.SourceApp = "SDKdemo";
    return new AugnitoSDK(appLogic);

}
this.GetAugnitoClient = GetAugnitoClient;
