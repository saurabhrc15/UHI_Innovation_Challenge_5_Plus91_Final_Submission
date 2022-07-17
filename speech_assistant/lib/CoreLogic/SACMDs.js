// These are few list of commands that augnito detect, It can be configurable at server.
var AugnitoConst = {};
AugnitoConst.LAST = 'last';
AugnitoConst.PREVIOUS = 'previous';
var AugnitoCMDs = {};
AugnitoCMDs.BOLD_IT = "boldit"; 
AugnitoCMDs.UN_BOLD_IT = "unboldit";
AugnitoCMDs.UNDERLINE_IT = "underlineit";
AugnitoCMDs.ITALICIZE_IT = "italicizeit"; 
AugnitoCMDs.UNITALICIZE_IT = "unitalicizeit";
AugnitoCMDs.COPY_IT = "copyit"; 
AugnitoCMDs.CUT_IT = "cutit";
AugnitoCMDs.PASTE_IT = "pasteit";
AugnitoCMDs.PRESS_DELETE = "pressdelete"; 
AugnitoCMDs.HEADER_IT = "headerit";
AugnitoCMDs.CAPITALIZED_IT = "capitalizeit"; 
AugnitoCMDs.UN_CAPITALIZED_IT = "uncapitalizeit";
AugnitoCMDs.UNDO_IT = "undoit"; 
AugnitoCMDs.REDO_IT = "redoit";
AugnitoCMDs.SELECT_PREVIOUS_WORD = "selectpreviousword"; 
AugnitoCMDs.SELECT_NEXT_WORD = "selectnextword";
AugnitoCMDs.SELECT_WORD = "selectword";
AugnitoCMDs.SELECT_ALL = "selectall";
AugnitoCMDs.SELECT = "select";
AugnitoCMDs.DESELECT_IT = "deselectit";
AugnitoCMDs.GO_TO_LINE_START = "gotolinestart";
AugnitoCMDs.GO_TO_LINE_END = "gotolineend";
AugnitoCMDs.SELECT_ACTIVE_LINE = "selectactiveline"; 
AugnitoCMDs.SELECT_ACTIVE_PARAGRAPH = "selectactiveparagraph"; 
AugnitoCMDs.SELECT_ACTIVE_WORD = "selectactiveword";
AugnitoCMDs.SELECT_ACTIVE_SENTENCE = "selectactivesentence"; 
AugnitoCMDs.SELECT_ACTIVE_CHAR = "selectactivechar";
AugnitoCMDs.BULLET_LIST_START = "bulletliststart"; 
AugnitoCMDs.NUMBER_LIST_START = "numberliststart"; 
AugnitoCMDs.BULLET_LIST_STOP = "bulletliststop";
AugnitoCMDs.NUMBER_LIST_STOP = "numberliststop";
AugnitoCMDs.LIST_STOP = "stoplist";
AugnitoCMDs.ALIGN_LEFT = "alignleft";
AugnitoCMDs.ALIGN_RIGHT = "alignright"; 
AugnitoCMDs.ALIGN_CENTER = "aligncenter";
AugnitoCMDs.ALIGN_JUSTIFY = "alignjustify";
AugnitoCMDs.STOP_MIC = "stopmic";
AugnitoCMDs.DYNAMIC_NEXT_FIELD = "dynamicfieldnext"; 
AugnitoCMDs.DYNAMIC_PREVIOUS_FIELD = "dynamicfieldprevious";
AugnitoCMDs.GO_UP = "goup"; 
AugnitoCMDs.GO_DOWN = "godown";
AugnitoCMDs.GO_RIGHT = "goright"; 
AugnitoCMDs.GO_LEFT = "goleft";
AugnitoCMDs.TAB_SPACE_ADD = "tabspaceadd";
AugnitoCMDs.SPACE_ADD = "spaceadd";
AugnitoCMDs.BACKSPACE = "backspace";
AugnitoCMDs.START_BOLD_TEXT = "boldstart";
AugnitoCMDs.STOP_BOLD_TEXT = "boldstop";
AugnitoCMDs.GO_TO_DOCUMENT_START = "gotodocumentstart";
AugnitoCMDs.GO_TO_DOCUMENT_END = "gotodocumentend";
AugnitoCMDs.GO_TO_NEXT_PAGE = "gotonextpage";
AugnitoCMDs.GO_TO_PREVIOUS_PAGE = "gotopreviouspage";
AugnitoCMDs.OK = "ok";
AugnitoCMDs.DOCUMENT_SAVE = "documentsave";
AugnitoCMDs.DOCUMENT_PRINT = "documentprint"; 
AugnitoCMDs.NEW_DOCUMENT = "newdocument";
AugnitoCMDs.NEXT_LINE_TEXT = "@newline@";
AugnitoCMDs.SELECT_LINE = "selectline"
AugnitoCMDs.SELECT_SENTENCE = "selectsentence";
AugnitoCMDs.DELETE="delete";
AugnitoCMDs.GOTO = "goto";
AugnitoCMDs.DELETE_PREVIOUS_WORD="deletepreviousword";
AugnitoCMDs.UNDERLIE_PREVIOUS_WORD="underlinepreviousword";
AugnitoCMDs.CAPITALIZE_PREVIOUS_WORD = "capitalizepreviousword";
AugnitoCMDs.ITALICIZE_PREVIOUS_WORD = "italicizepreviousword";
AugnitoCMDs.BOLD_PREVIOUS_WORD="boldpreviousword";

AugnitoCMDs.DELETE_NEXT_WORD = "deletenextword";
AugnitoCMDs.DELETE_PREVIOUS_LINE="deletepreviousline";
AugnitoCMDs.SELECT_PREVIOUS_LINE="selectpreviousline";

AugnitoCMDs.SELECT_NEXT_LINE="selectnextline";

AugnitoCMDs.SETFONTSIZEN = "setfontsize";
AugnitoCMDs.INSERT_BEFORE_TEXT = "insertbefore";
AugnitoCMDs.INSERT_AFTER_TEXT = "insertafter";

AugnitoCMDs.DELETE_PREVIOUS_SENTENCE="deleteprevioussentence";
AugnitoCMDs.SELECT_PREVIOUS_SENTENCE="selectprevioussentence";
AugnitoCMDs.SELECT_NEXT_SENTENCE="selectnextsentence"

AugnitoCMDs.START_CAPITAL_TEXT = "capitalizestart";
AugnitoCMDs.STOP_CAPITAL_TEXT = "capitalizestop";

AugnitoCMDs.NEXT_FIELD = "dynamicfieldnext";
AugnitoCMDs.PREVIOUS_FIELD = "dynamicfieldprevious";

AugnitoCMDs.SEARCH_MEDICINE = "searchmedicine";
AugnitoCMDs.SELECT_MEDICINE = "selectmedicine";
AugnitoCMDs.SELECT_MEDICINE_NUMBER = "selectmedicinenumber";
AugnitoCMDs.ADD_ANOTHER_MEDICINE = "addanothermedicine";

AugnitoCMDs.SEARCH_COMPLAINT = "searchcomplaint";
AugnitoCMDs.SELECT_COMPLAINT = "selectcomplaint";
AugnitoCMDs.SELECT_COMPLAINT_NUMBER = "selectcomplaintnumber";
AugnitoCMDs.ADD_ANOTHER_COMPLAINT = "addanothercomplaint";

AugnitoCMDs.SEARCH_ICD10 = "searchicd10code";
AugnitoCMDs.SELECT_ICD10_CODE_NUMBER = "selecticd10codenumber";