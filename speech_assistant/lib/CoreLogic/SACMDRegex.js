// Augnito server only detect static word and send only action name to client to perform action.
// Static commands does not have any place holder for dynamic value in action.
// Client can access it

var ACTION_CMD = "select|choose|copy text|copytext|cut text|cuttext|correct|bold|underline|delete|header|capitalize|unbold|debold|dbold|uncapitalize|remove|capitalise|dcapitalise|dcapitalize|decapitalize|decapitalise|uncapitalise|Uncap|d capitalise|d capitalize|d underline|dunderline|deunderline|ununderline|goto|moveto|move|italicize|italicise|unitalicise|unitalicize|search";
var  AugnitoCMDRegex= {};
AugnitoCMDRegex.PrepareRecipe = function (recipe) {

    //Dynamic commands
    //Normalize It, \n Get removed in trim so
    var cmdtext = recipe.ReceivedText.replace(/\n/gi, AugnitoCMDs.NEXT_LINE_TEXT);
    cmdtext = cmdtext.replace(/ /gi, "").toLowerCase().trim();
    cmdtext = cmdtext.replace(new RegExp(AugnitoCMDs.NEXT_LINE_TEXT, "gi"), "\n");
    recipe.Name = cmdtext;
    recipe.ReceivedTextWithoutSpace = cmdtext; // Name may modify by dictionary,cmd and regex, But this one is same as received text.
    return FillDynamicCommand(recipe, cmdtext);
};

function FillDynamicCommand(recipe, CommandText) {

    //action on active paragraph, line, word or 
    var LINE_PARAGRAPH_NUMBER = new RegExp("^(" + ACTION_CMD + ")(the)?(line|para|paragraph|\n\n|\n)(number[ed]?)(.*?)(\.)?$", "gi");
    var LINE_PARAGRAPH_NUMBER_Matches = LINE_PARAGRAPH_NUMBER.exec(CommandText.trim());
    if (LINE_PARAGRAPH_NUMBER_Matches && LINE_PARAGRAPH_NUMBER_Matches.length > 3) {
        var cmdAction = LINE_PARAGRAPH_NUMBER_Matches[1];
        var lineOrPara = LINE_PARAGRAPH_NUMBER_Matches[3];
        cmdAction = SetActionCommandVariant(cmdAction.toLowerCase());
        var lineNumber = TextWordToInteger.Parse(LINE_PARAGRAPH_NUMBER_Matches[6]);
        if (cmdAction && lineNumber > 0) {
            recipe.Name = (("line" == lineOrPara) || ("\n" == lineOrPara)) ? AugnitoCMDs.GO_TO_LINE_NUMBER : AugnitoCMDs.GO_TO_PARA_NUMBER;
            recipe.ChooseNumber = lineNumber;
            recipe.ReceivedText = "";
            recipe.SearchText = lineOrPara;
            recipe.IsCommand = true;
            recipe.SelectFor = AugnitoCMDs.SELECT == cmdAction ? "" : cmdAction;
            return recipe;
        }
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.SEARCH_MEDICINE)) {
        // Go to next line
        recipe.Name = AugnitoCMDs.SEARCH_MEDICINE;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "searchmedicine";
        recipe.SearchText = CommandText.replace("searchmedicine", "").trim();
        return recipe;
    }

    if (CommandText.trim() == AugnitoCMDs.ADD_ANOTHER_MEDICINE) {
        // Go to next line
        recipe.Name = AugnitoCMDs.ADD_ANOTHER_MEDICINE;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "addanothermedicine";
        recipe.SearchText = "addanothermedicine";
        return recipe;
    }

    if (CommandText.trim() == AugnitoCMDs.ADD_ANOTHER_COMPLAINT) {
        // Go to next line
        recipe.Name = AugnitoCMDs.ADD_ANOTHER_COMPLAINT;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "addanothercomplaint";
        recipe.SearchText = "addanothercomplaint";
        return recipe;
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.SEARCH_COMPLAINT)) {
        // Go to next line
        recipe.Name = AugnitoCMDs.SEARCH_COMPLAINT;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "searchcomplaint";
        recipe.SearchText = CommandText.replace("searchcomplaint", "").trim();
        return recipe;
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.SEARCH_ICD10)) {
        // Go to next line
        recipe.Name = AugnitoCMDs.SEARCH_ICD10;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "searchicd10code";
        recipe.SearchText = CommandText.replace("searchicd10code", "").trim();
        return recipe;
    }

    var aWordNumbers = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth'];
    var SELECT_MEDICINE_REGEX = new RegExp("^(select)?(" + aWordNumbers.join("|") + "?)(medicine)", "gi");
    var aSelectMedicineCommandMatches = SELECT_MEDICINE_REGEX.exec(CommandText.trim());


    if (aSelectMedicineCommandMatches && aSelectMedicineCommandMatches.length > 2) {

        // Go to next line
        recipe.Name = AugnitoCMDs.SELECT_MEDICINE;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "selecthmedicine";
        recipe.SearchText = aSelectMedicineCommandMatches[2].trim();
        return recipe;
    }

    var SELECT_COMPLAINT_REGEX = new RegExp("^(select)?(" + aWordNumbers.join("|") + "?)(complaint)", "gi");
    var aSelectChiefComplaintCommandMatches = SELECT_COMPLAINT_REGEX.exec(CommandText.trim());

    if (aSelectChiefComplaintCommandMatches && aSelectChiefComplaintCommandMatches.length > 2) {

        recipe.Name = AugnitoCMDs.SELECT_COMPLAINT;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "selectcomplaint";
        recipe.SearchText = aSelectChiefComplaintCommandMatches[2].trim();
        return recipe;
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.SELECT_MEDICINE_NUMBER)) {
        recipe.Name = AugnitoCMDs.SELECT_MEDICINE_NUMBER;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "selectmedicinenumber";
        recipe.SearchText = CommandText.replace("selectmedicinenumber", "").trim();
        return recipe;
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.SELECT_COMPLAINT_NUMBER)) {
        recipe.Name = AugnitoCMDs.SELECT_COMPLAINT_NUMBER;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "selectcomplaintnumber";
        recipe.SearchText = CommandText.replace("selectcomplaintnumber", "").trim();
        return recipe;
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.SELECT_ICD10_CODE_NUMBER)) {
        recipe.Name = AugnitoCMDs.SELECT_ICD10_CODE_NUMBER;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "selecticd10codenumber";
        recipe.SearchText = CommandText.replace("selecticd10codenumber", "").trim();
        return recipe;
    }

    if ("goto\n" == CommandText || "move\n" == CommandText || "moveto\n" == CommandText) {
        // Go to next line
        recipe.Name = AugnitoCMDs.SELECT_LINE;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "gotoend";
        return recipe;
    }
    if ("delete\n" == CommandText) {
        // Go to next Para
        recipe.Name = AugnitoCMDs.SELECT_LINE;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.IsCommand = true;
        recipe.SelectFor = "delete";
        return recipe;
    }

    if ("goto\n\n" == CommandText || "move\n\n" == CommandText || "moveto\n\n" == CommandText) {
        // Go to next Para
        recipe.Name = AugnitoCMDs.SELECT_PARAGRAPH;
        recipe.NextPrevious = "next";
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = "gotoend";
        return recipe;
    }

    var ACTIVE_X = new RegExp("^(" + ACTION_CMD + ")(the)?(active|current)?(word[s]?|line[s]?|sentence[s]?|paragraph[s]?|para[s]?|char[s]?|character[s]?|space|\n\n|\n)$", "gi");
    var ACTIVE_X_Matches = ACTIVE_X.exec(CommandText.trim());
    if (ACTIVE_X_Matches && ACTIVE_X_Matches.length > 4) {
        var cmdAction = ACTIVE_X_Matches[1];
        cmdAction = SetActionCommandVariant(cmdAction);
        var item = ACTIVE_X_Matches[4].toLowerCase();
        if (item && cmdAction) {
            recipe.Name = SetActiveObjectType(item);
            recipe.ReceivedText = "";
            recipe.SearchText = item;
            recipe.IsCommand = true;
            recipe.SelectFor = AugnitoCMDs.SELECT == cmdAction ? "" : cmdAction;
            return recipe;
        }
    }

    var NAVIGATION_WITHOUT_GO_TO = new RegExp("^(last|previous|next|down)(.*?)(word[s]?|line[s]?|sentence[s]?|paragraph[s]?|para[s]?|char[s]?|character[s]?|space|\n\n|\n)$", "gi");
    var NavigationWithoutGoToMatches = NAVIGATION_WITHOUT_GO_TO.exec(CommandText.trim());
    if (NavigationWithoutGoToMatches && NavigationWithoutGoToMatches.length > 3) {
        var direction = NavigationWithoutGoToMatches[1];
        var item = NavigationWithoutGoToMatches[3].toLowerCase();
        recipe.Name = SetSelectObjectType(item);
        recipe.NextPrevious = direction;
        recipe.ChooseNumber = 1;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = ("previous" == direction || "last" == direction) ? "gotostart" : "gotoend";
        return recipe;
    }

    var GO_TO_X = new RegExp("^(go|goto|gotothe|move|moveto|movethe)(last|previous|next|down)(.*?)(word[s]?|line[s]?|sentence[s]?|paragraph[s]?|para[s]?|char[s]?|character[s]?|space|\n\n|\n)$", "gi");
    var StartOfXMatches = GO_TO_X.exec(CommandText.trim());
    if (StartOfXMatches && StartOfXMatches.length > 3) {
        var cmdAction = StartOfXMatches[1];
        var direction = StartOfXMatches[2];
        var wordCount = TextWordToInteger.Parse(StartOfXMatches[3]);
        var item = StartOfXMatches[4].toLowerCase();
        recipe.Name = SetSelectObjectType(item);
        recipe.NextPrevious = direction;
        recipe.ChooseNumber = wordCount;
        recipe.ReceivedText = "";
        recipe.IsCommand = true;
        recipe.SelectFor = ("previous" == direction || "last" == direction) ? "gotostart" : "gotoend";
        return recipe;
    }

    var SELECT_WORD_LINE_NEXT_PREVIOUS = new RegExp("^(" + ACTION_CMD + ")(the)?(last|previous|next)(.*?)(word[s]?|line[s]?|sentence[s]?|paragraph[s]?|para[s]?|char[s]?|character[s]?|space|\n\n|\n)$", "gi");
    var SelectManyWordMatches = SELECT_WORD_LINE_NEXT_PREVIOUS.exec(CommandText);
    if (SelectManyWordMatches && SelectManyWordMatches.length > 3) {
        var cmdAction = SetActionCommandVariant(SelectManyWordMatches[1].toLowerCase());
        var direction = SelectManyWordMatches[3];
        var wordCount = TextWordToInteger.Parse(SelectManyWordMatches[4]);
        wordCount = (wordCount == 0 ? 1 : wordCount);
        var item = SelectManyWordMatches[5].toLowerCase();
        if (item && cmdAction && direction) {
            recipe.Name = SetSelectObjectType(item);
            recipe.NextPrevious = direction;
            recipe.ChooseNumber = wordCount;
            recipe.ReceivedText = "";
            recipe.SearchText = item;
            recipe.IsCommand = true;
            recipe.SelectFor = AugnitoCMDs.SELECT == cmdAction ? "" : cmdAction;
            return recipe;
        }
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.SETFONTSIZEN)) {
        var SET_FONT_SIZE = new RegExp("^setfontsize(to)?([0-9]+)(point[s]?)?$", "gi");
        var SET_FONT_SIZE_Matches = SET_FONT_SIZE.exec(CommandText.trim());
        if (SET_FONT_SIZE_Matches && SET_FONT_SIZE_Matches.length > 2) {
            recipe.Name = AugnitoCMDs.SETFONTSIZEN;
            recipe.FontSize = TextWordToInteger.Parse(SET_FONT_SIZE_Matches[2]);
            recipe.IsCommand = true;
            return recipe;
        }
    }

    //var SELECT_GROUP_2 = new RegExp("^(" + ACTION_CMD + ")(\sthe)?\s(.*?)$", "gi");
    var SELECT_GROUP_2 = new RegExp("^(" + ACTION_CMD + ")(the)?(.*?)$", "gi");
    var SelectMatches = SELECT_GROUP_2.exec(recipe.ReceivedText.trim());
    if (SelectMatches && SelectMatches.length > 3) {
        var cmdAction = SelectMatches[1];
        cmdAction = SetActionCommandVariant(cmdAction.toLowerCase());
        var searchText = SelectMatches[3];
        if (searchText) {
            recipe.Name = AugnitoCMDs.SELECT;
            recipe.SearchText = searchText.trim();
            recipe.IsCommand = true;
            recipe.SelectFor = AugnitoCMDs.SELECT == cmdAction ? "" : cmdAction;
            return recipe;
        }
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.INSERT_BEFORE_TEXT)) {
        recipe.Name = AugnitoCMDs.INSERT_BEFORE_TEXT;
        recipe.SearchText = recipe.ReceivedText.trim();
        recipe.SearchText = recipe.SearchText.trim().substr(6);
        recipe.SearchText = recipe.SearchText.trim().substr(6).trim();
        recipe.IsCommand = true;
        return recipe;
    }

    if (CommandText.trim().startsWith(AugnitoCMDs.INSERT_AFTER_TEXT)) {
        recipe.Name = AugnitoCMDs.INSERT_AFTER_TEXT;
        recipe.SearchText = recipe.ReceivedText.trim();
        recipe.SearchText = recipe.SearchText.trim().substr(6);
        recipe.SearchText = recipe.SearchText.trim().substr(5).trim();
        recipe.IsCommand = true;
        return recipe;
    }

    if (recipe.ReceivedText.length < 12) {
        var NUMBER_CHOOSE = new RegExp("^(number)?([0-9]+|x)[.]?$", "gi");
        var NUMBER_CHOOSE_Matches = NUMBER_CHOOSE.exec(CommandText.trim());
        if (NUMBER_CHOOSE_Matches && NUMBER_CHOOSE_Matches.length > 2) {
            var dig = NUMBER_CHOOSE_Matches[2];
            dig = "x" == dig ? "10" : dig;
            recipe.Name = AugnitoCMDs.NUMBER;
            recipe.ChooseNumber = TextWordToInteger.Parse(dig);
            recipe.IsCommand = false; // Will consider this is command when system is some selection mode otherwise dictation.
            return recipe;
        }
    }

    if (recipe.Name.startsWith(AugnitoCMDs.GOTO)) {
        recipe.Name = AugnitoCMDs.GOTO;
        recipe.SearchText = recipe.ReceivedText.trim().substr(2);
        recipe.SearchText = recipe.SearchText.trim().substr(2).trim();
        recipe.IsCommand = true;
        return recipe;
    }

    return recipe;
}

function SetActionCommandVariant(cmdAction) {
    return "d capitalise" == cmdAction ? "uncapitalize" :
        "d capitalize" == cmdAction ? "uncapitalize" :
            "decapitalize" == cmdAction ? "uncapitalize" :
                "decapitalise" == cmdAction ? "uncapitalize" :
                    "dcapitalise" == cmdAction ? "uncapitalize" :
                        "dcapitalize" == cmdAction ? "uncapitalize" :
                            "uncapitalize" == cmdAction ? "uncapitalize" :
                                "uncapitalise" == cmdAction ? "uncapitalize" :
                                    "uncap" == cmdAction ? "uncapitalize" :
                                        "d underline" == cmdAction ? "deunderline" :
                                            "dunderline" == cmdAction ? "deunderline" :
                                                "dunderline" == cmdAction ? "dunderline" :
                                                    "ununderline" == cmdAction ? "deunderline" :
                                                        "capitalise" == cmdAction ? "capitalize" :
                                                            "remove" == cmdAction ? "delete" :
                                                                "goto" == cmdAction ? "gotostart" :
                                                                    "move" == cmdAction ? "gotostart" :
                                                                        "debold" == cmdAction ? "unbold" :
                                                                            "dbold" == cmdAction ? "unbold" :
                                                                                "cuttext" == cmdAction ? "cut" :
                                                                                    "cut text" == cmdAction ? "cut" :
                                                                                        "copytext" == cmdAction ? "copy" :
                                                                                            "copy text" == cmdAction ? "copy" :
                                                                                                "choose" == cmdAction ? "select" :
                                                                                                    "italicise" == cmdAction ? "italicize" :
                                                                                                        "unitalicise" == cmdAction ? "unitalicize" :
                                                                                                            "movto" == cmdAction ? "gotostart" : cmdAction;
}

function SetActiveObjectType(item) {
    return "word" == item || "words" == item ? AugnitoCMDs.SELECT_ACTIVE_WORD :
        "sentence" == item || "sentences" == item ? AugnitoCMDs.SELECT_ACTIVE_SENTENCE :
            "line" == item || "lines" == item ? AugnitoCMDs.SELECT_ACTIVE_LINE :
                "paragraph" == item || "paragraphs" == item ? AugnitoCMDs.SELECT_ACTIVE_PARAGRAPH :
                    "para" == item || "paras" == item ? AugnitoCMDs.SELECT_ACTIVE_PARAGRAPH :
                        "\n\n" == item ? AugnitoCMDs.SELECT_ACTIVE_PARAGRAPH :
                            "\n" == item ? AugnitoCMDs.SELECT_LINE :
                                "char" == item || "chars" == item ? AugnitoCMDs.SELECT_ACTIVE_CHAR :
                                    "space" == item ? AugnitoCMDs.SELECT_ACTIVE_CHAR :
                                        "character" == item || "characters" == item ? AugnitoCMDs.SELECT_ACTIVE_CHAR : item;
}

function SetSelectObjectType(item) {
    return "word" == item || "words" == item ? AugnitoCMDs.SELECT_WORD :
        "sentence" == item || "sentences" == item ? AugnitoCMDs.SELECT_SENTENCE :
            "line" == item || "lines" == item ? AugnitoCMDs.SELECT_LINE :
                "paragraph" == item || "paragraphs" == item ? AugnitoCMDs.SELECT_PARAGRAPH :
                    "para" == item || "paras" == item ? AugnitoCMDs.SELECT_PARAGRAPH :
                        "\n\n" == item ? AugnitoCMDs.SELECT_PARAGRAPH :
                            "\n" == item ? AugnitoCMDs.SELECT_LINE :
                                "char" == item || "chars" == item ? AugnitoCMDs.SELECT_CHAR :
                                    "space" == item ? AugnitoCMDs.SELECT_CHAR :
                                        "character" == item || "characters" == item ? AugnitoCMDs.SELECT_CHAR : item;
}