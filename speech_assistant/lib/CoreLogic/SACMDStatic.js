// Server will detect this commands and sent output
var AugnitoCMDStatic = {};
AugnitoCMDStatic.PrepareRecipe = function (recipe) {

    //Normalize It, \n Get removed in trim so
    var cmdtext = recipe.ReceivedText.replace(/\n/gi, AugnitoCMDs.NEXT_LINE_TEXT);
    cmdtext = cmdtext.replace(/ /gi, "").toLowerCase().trim();
    cmdtext = cmdtext.replace(new RegExp(AugnitoCMDs.NEXT_LINE_TEXT, "gi"), "\n");
    recipe.Name = cmdtext;
    recipe.ReceivedTextWithoutSpace = cmdtext; // Name may modify by dictionary,cmd and regex, But this one is same as received text.

    if (AugnitoCMDs.DELETE_PREVIOUS_WORD == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_WORD;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "previous";
        recipe.SelectFor = "delete";
        return recipe;
    }

    if (AugnitoCMDs.DELETE_PREVIOUS_LINE == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_LINE;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "previous";
        recipe.SelectFor = "delete";
        return recipe;
    }

    if (AugnitoCMDs.SELECT_PREVIOUS_LINE == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_LINE;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "previous";
        return recipe;
    }
    if (AugnitoCMDs.SELECT_NEXT_LINE == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_LINE;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "next";
        return recipe;
    }

    if (AugnitoCMDs.DELETE_PREVIOUS_SENTENCE == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_SENTENCE;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "previous";
        recipe.SelectFor = "delete";
        return recipe;
    }

    if (AugnitoCMDs.SELECT_NEXT_SENTENCE == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_SENTENCE;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "next";
        return recipe;
    }
    if (AugnitoCMDs.SELECT_PREVIOUS_SENTENCE == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_SENTENCE;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "previous";
        return recipe;
    }

    if (AugnitoCMDs.BOLD_PREVIOUS_WORD == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_WORD;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "last";
        recipe.SelectFor = "bold";
        return recipe;
    }

    if (AugnitoCMDs.CAPITALIZE_PREVIOUS_WORD == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_WORD;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "last";
        recipe.SelectFor = "capitalize";
        return recipe;
    }

    if (AugnitoCMDs.ITALICIZE_PREVIOUS_WORD == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_WORD;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "last";
        recipe.SelectFor = "italicize";
        return recipe;
    }

    if (AugnitoCMDs.UNDERLIE_PREVIOUS_WORD == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_WORD;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "last";
        recipe.SelectFor = "underline";
        return recipe;
    }

    if (AugnitoCMDs.SELECT_PREVIOUS_WORD == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_WORD;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "last";
        recipe.SearchText = "previousword";
        recipe.SelectFor = "select";
        return recipe;
    }
    if (AugnitoCMDs.SELECT_ALL == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT;
        recipe.SearchText = "all";
        return recipe;
    }
    if (AugnitoCMDs.SELECT_NEXT_WORD == recipe.Action) {
        recipe.Name = AugnitoCMDs.SELECT_WORD;
        recipe.ChooseNumber = 1;
        recipe.NextPrevious = "next";
        recipe.SearchText = "nextword";
        return recipe;
    }
    return recipe;
};







