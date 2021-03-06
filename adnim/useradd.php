<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "userinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$user_add = NULL; // Initialize page object first

class cuser_add extends cuser {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{DE39BAA5-7315-4870-A01D-3D4074A06380}";

	// Table name
	var $TableName = 'user';

	// Page object name
	var $PageObjName = 'user_add';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			$html .= "<p class=\"ewMessage\">" . $sMessage . "</p>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewWarningIcon\"></td><td class=\"ewWarningMessage\">" . $sWarningMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewSuccessIcon\"></td><td class=\"ewSuccessMessage\">" . $sSuccessMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewErrorIcon\"></td><td class=\"ewErrorMessage\">" . $sErrorMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p class=\"phpmaker\">" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Fotoer exists, display
			echo "<p class=\"phpmaker\">" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language, $UserAgent;

		// User agent
		$UserAgent = ew_UserAgent();
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (user)
		if (!isset($GLOBALS["user"])) {
			$GLOBALS["user"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["user"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'user', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"];

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["id_user"] != "") {
				$this->id_user->setQueryStringValue($_GET["id_user"]);
				$this->setKey("id_user", $this->id_user->CurrentValue); // Set up key
			} else {
				$this->setKey("id_user", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("userlist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "userview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
		$index = $objForm->Index; // Save form index
		$objForm->Index = -1;
		$confirmPage = (strval($objForm->GetValue("a_confirm")) <> "");
		$objForm->Index = $index; // Restore form index
	}

	// Load default values
	function LoadDefaultValues() {
		$this->_email->CurrentValue = NULL;
		$this->_email->OldValue = $this->_email->CurrentValue;
		$this->name->CurrentValue = NULL;
		$this->name->OldValue = $this->name->CurrentValue;
		$this->surname->CurrentValue = NULL;
		$this->surname->OldValue = $this->surname->CurrentValue;
		$this->timestamp->CurrentValue = NULL;
		$this->timestamp->OldValue = $this->timestamp->CurrentValue;
		$this->token->CurrentValue = NULL;
		$this->token->OldValue = $this->token->CurrentValue;
		$this->fbid->CurrentValue = NULL;
		$this->fbid->OldValue = $this->fbid->CurrentValue;
		$this->gid->CurrentValue = NULL;
		$this->gid->OldValue = $this->gid->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->_email->FldIsDetailKey) {
			$this->_email->setFormValue($objForm->GetValue("x__email"));
		}
		if (!$this->name->FldIsDetailKey) {
			$this->name->setFormValue($objForm->GetValue("x_name"));
		}
		if (!$this->surname->FldIsDetailKey) {
			$this->surname->setFormValue($objForm->GetValue("x_surname"));
		}
		if (!$this->timestamp->FldIsDetailKey) {
			$this->timestamp->setFormValue($objForm->GetValue("x_timestamp"));
			$this->timestamp->CurrentValue = ew_UnFormatDateTime($this->timestamp->CurrentValue, 5);
		}
		if (!$this->token->FldIsDetailKey) {
			$this->token->setFormValue($objForm->GetValue("x_token"));
		}
		if (!$this->fbid->FldIsDetailKey) {
			$this->fbid->setFormValue($objForm->GetValue("x_fbid"));
		}
		if (!$this->gid->FldIsDetailKey) {
			$this->gid->setFormValue($objForm->GetValue("x_gid"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->_email->CurrentValue = $this->_email->FormValue;
		$this->name->CurrentValue = $this->name->FormValue;
		$this->surname->CurrentValue = $this->surname->FormValue;
		$this->timestamp->CurrentValue = $this->timestamp->FormValue;
		$this->timestamp->CurrentValue = ew_UnFormatDateTime($this->timestamp->CurrentValue, 5);
		$this->token->CurrentValue = $this->token->FormValue;
		$this->fbid->CurrentValue = $this->fbid->FormValue;
		$this->gid->CurrentValue = $this->gid->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->id_user->setDbValue($rs->fields('id_user'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->name->setDbValue($rs->fields('name'));
		$this->surname->setDbValue($rs->fields('surname'));
		$this->timestamp->setDbValue($rs->fields('timestamp'));
		$this->token->setDbValue($rs->fields('token'));
		$this->fbid->setDbValue($rs->fields('fbid'));
		$this->gid->setDbValue($rs->fields('gid'));
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id_user")) <> "")
			$this->id_user->CurrentValue = $this->getKey("id_user"); // id_user
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id_user
		// email
		// name
		// surname
		// timestamp
		// token
		// fbid
		// gid

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_user
			$this->id_user->ViewValue = $this->id_user->CurrentValue;
			$this->id_user->ViewCustomAttributes = "";

			// email
			$this->_email->ViewValue = $this->_email->CurrentValue;
			$this->_email->ViewCustomAttributes = "";

			// name
			$this->name->ViewValue = $this->name->CurrentValue;
			$this->name->ViewCustomAttributes = "";

			// surname
			$this->surname->ViewValue = $this->surname->CurrentValue;
			$this->surname->ViewCustomAttributes = "";

			// timestamp
			$this->timestamp->ViewValue = $this->timestamp->CurrentValue;
			$this->timestamp->ViewValue = ew_FormatDateTime($this->timestamp->ViewValue, 5);
			$this->timestamp->ViewCustomAttributes = "";

			// token
			$this->token->ViewValue = $this->token->CurrentValue;
			$this->token->ViewCustomAttributes = "";

			// fbid
			$this->fbid->ViewValue = $this->fbid->CurrentValue;
			$this->fbid->ViewCustomAttributes = "";

			// gid
			$this->gid->ViewValue = $this->gid->CurrentValue;
			$this->gid->ViewCustomAttributes = "";

			// email
			$this->_email->LinkCustomAttributes = "";
			$this->_email->HrefValue = "";
			$this->_email->TooltipValue = "";

			// name
			$this->name->LinkCustomAttributes = "";
			$this->name->HrefValue = "";
			$this->name->TooltipValue = "";

			// surname
			$this->surname->LinkCustomAttributes = "";
			$this->surname->HrefValue = "";
			$this->surname->TooltipValue = "";

			// timestamp
			$this->timestamp->LinkCustomAttributes = "";
			$this->timestamp->HrefValue = "";
			$this->timestamp->TooltipValue = "";

			// token
			$this->token->LinkCustomAttributes = "";
			$this->token->HrefValue = "";
			$this->token->TooltipValue = "";

			// fbid
			$this->fbid->LinkCustomAttributes = "";
			$this->fbid->HrefValue = "";
			$this->fbid->TooltipValue = "";

			// gid
			$this->gid->LinkCustomAttributes = "";
			$this->gid->HrefValue = "";
			$this->gid->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// email
			$this->_email->EditCustomAttributes = "";
			$this->_email->EditValue = ew_HtmlEncode($this->_email->CurrentValue);

			// name
			$this->name->EditCustomAttributes = "";
			$this->name->EditValue = ew_HtmlEncode($this->name->CurrentValue);

			// surname
			$this->surname->EditCustomAttributes = "";
			$this->surname->EditValue = ew_HtmlEncode($this->surname->CurrentValue);

			// timestamp
			$this->timestamp->EditCustomAttributes = "";
			$this->timestamp->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->timestamp->CurrentValue, 5));

			// token
			$this->token->EditCustomAttributes = "";
			$this->token->EditValue = ew_HtmlEncode($this->token->CurrentValue);

			// fbid
			$this->fbid->EditCustomAttributes = "";
			$this->fbid->EditValue = ew_HtmlEncode($this->fbid->CurrentValue);

			// gid
			$this->gid->EditCustomAttributes = "";
			$this->gid->EditValue = ew_HtmlEncode($this->gid->CurrentValue);

			// Edit refer script
			// email

			$this->_email->HrefValue = "";

			// name
			$this->name->HrefValue = "";

			// surname
			$this->surname->HrefValue = "";

			// timestamp
			$this->timestamp->HrefValue = "";

			// token
			$this->token->HrefValue = "";

			// fbid
			$this->fbid->HrefValue = "";

			// gid
			$this->gid->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!is_null($this->_email->FormValue) && $this->_email->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->_email->FldCaption());
		}
		if (!is_null($this->name->FormValue) && $this->name->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->name->FldCaption());
		}
		if (!is_null($this->surname->FormValue) && $this->surname->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->surname->FldCaption());
		}
		if (!ew_CheckDate($this->timestamp->FormValue)) {
			ew_AddMessage($gsFormError, $this->timestamp->FldErrMsg());
		}
		if (!is_null($this->token->FormValue) && $this->token->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->token->FldCaption());
		}
		if (!ew_CheckInteger($this->fbid->FormValue)) {
			ew_AddMessage($gsFormError, $this->fbid->FldErrMsg());
		}
		if (!ew_CheckInteger($this->gid->FormValue)) {
			ew_AddMessage($gsFormError, $this->gid->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;
		if ($this->_email->CurrentValue <> "") { // Check field with unique index
			$sFilter = "(email = '" . ew_AdjustSql($this->_email->CurrentValue) . "')";
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->_email->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->_email->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
		}
		$rsnew = array();

		// email
		$this->_email->SetDbValueDef($rsnew, $this->_email->CurrentValue, "", FALSE);

		// name
		$this->name->SetDbValueDef($rsnew, $this->name->CurrentValue, "", FALSE);

		// surname
		$this->surname->SetDbValueDef($rsnew, $this->surname->CurrentValue, "", FALSE);

		// timestamp
		$this->timestamp->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->timestamp->CurrentValue, 5), NULL, FALSE);

		// token
		$this->token->SetDbValueDef($rsnew, $this->token->CurrentValue, "", FALSE);

		// fbid
		$this->fbid->SetDbValueDef($rsnew, $this->fbid->CurrentValue, NULL, FALSE);

		// gid
		$this->gid->SetDbValueDef($rsnew, $this->gid->CurrentValue, NULL, FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->id_user->setDbValue($conn->Insert_ID());
			$rsnew['id_user'] = $this->id_user->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($user_add)) $user_add = new cuser_add();

// Page init
$user_add->Page_Init();

// Page main
$user_add->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var user_add = new ew_Page("user_add");
user_add.PageID = "add"; // Page ID
var EW_PAGE_ID = user_add.PageID; // For backward compatibility

// Form object
var fuseradd = new ew_Form("fuseradd");

// Validate form
fuseradd.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();	
	if (fobj.a_confirm && fobj.a_confirm.value == "F")
		return true;
	var elm, aelm;
	var rowcnt = 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // rowcnt == 0 => Inline-Add
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = "";
		elm = fobj.elements["x" + infix + "__email"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user->_email->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_name"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user->name->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_surname"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user->surname->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_timestamp"];
		if (elm && !ew_CheckDate(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($user->timestamp->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_token"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user->token->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_fbid"];
		if (elm && !ew_CheckInteger(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($user->fbid->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_gid"];
		if (elm && !ew_CheckInteger(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($user->gid->FldErrMsg()) ?>");

		// Set up row object
		ew_ElementsToRow(fobj, infix);

		// Fire Form_CustomValidate event
		if (!this.Form_CustomValidate(fobj))
			return false;
	}

	// Process detail page
	if (fobj.detailpage && fobj.detailpage.value && ewForms[fobj.detailpage.value])
		return ewForms[fobj.detailpage.value].Validate(fobj);
	return true;
}

// Form_CustomValidate event
fuseradd.Form_CustomValidate =  
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fuseradd.ValidateRequired = true;
<?php } else { ?>
fuseradd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Add") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $user->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $user->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $user_add->ShowPageHeader(); ?>
<?php
$user_add->ShowMessage();
?>
<form name="fuseradd" id="fuseradd" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="user">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_useradd" class="ewTable">
<?php if ($user->_email->Visible) { // email ?>
	<tr id="r__email"<?php echo $user->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_user__email"><table class="ewTableHeaderBtn"><tr><td><?php echo $user->_email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $user->_email->CellAttributes() ?>><span id="el_user__email">
<input type="text" name="x__email" id="x__email" size="30" maxlength="200" value="<?php echo $user->_email->EditValue ?>"<?php echo $user->_email->EditAttributes() ?>>
</span><?php echo $user->_email->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user->name->Visible) { // name ?>
	<tr id="r_name"<?php echo $user->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_user_name"><table class="ewTableHeaderBtn"><tr><td><?php echo $user->name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $user->name->CellAttributes() ?>><span id="el_user_name">
<input type="text" name="x_name" id="x_name" size="30" maxlength="200" value="<?php echo $user->name->EditValue ?>"<?php echo $user->name->EditAttributes() ?>>
</span><?php echo $user->name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user->surname->Visible) { // surname ?>
	<tr id="r_surname"<?php echo $user->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_user_surname"><table class="ewTableHeaderBtn"><tr><td><?php echo $user->surname->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $user->surname->CellAttributes() ?>><span id="el_user_surname">
<input type="text" name="x_surname" id="x_surname" size="30" maxlength="200" value="<?php echo $user->surname->EditValue ?>"<?php echo $user->surname->EditAttributes() ?>>
</span><?php echo $user->surname->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user->timestamp->Visible) { // timestamp ?>
	<tr id="r_timestamp"<?php echo $user->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_user_timestamp"><table class="ewTableHeaderBtn"><tr><td><?php echo $user->timestamp->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $user->timestamp->CellAttributes() ?>><span id="el_user_timestamp">
<input type="text" name="x_timestamp" id="x_timestamp" value="<?php echo $user->timestamp->EditValue ?>"<?php echo $user->timestamp->EditAttributes() ?>>
</span><?php echo $user->timestamp->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user->token->Visible) { // token ?>
	<tr id="r_token"<?php echo $user->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_user_token"><table class="ewTableHeaderBtn"><tr><td><?php echo $user->token->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $user->token->CellAttributes() ?>><span id="el_user_token">
<textarea name="x_token" id="x_token" cols="35" rows="4"<?php echo $user->token->EditAttributes() ?>><?php echo $user->token->EditValue ?></textarea>
</span><?php echo $user->token->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user->fbid->Visible) { // fbid ?>
	<tr id="r_fbid"<?php echo $user->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_user_fbid"><table class="ewTableHeaderBtn"><tr><td><?php echo $user->fbid->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $user->fbid->CellAttributes() ?>><span id="el_user_fbid">
<input type="text" name="x_fbid" id="x_fbid" size="30" value="<?php echo $user->fbid->EditValue ?>"<?php echo $user->fbid->EditAttributes() ?>>
</span><?php echo $user->fbid->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user->gid->Visible) { // gid ?>
	<tr id="r_gid"<?php echo $user->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_user_gid"><table class="ewTableHeaderBtn"><tr><td><?php echo $user->gid->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $user->gid->CellAttributes() ?>><span id="el_user_gid">
<input type="text" name="x_gid" id="x_gid" size="30" value="<?php echo $user->gid->EditValue ?>"<?php echo $user->gid->EditAttributes() ?>>
</span><?php echo $user->gid->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="btnAction" id="btnAction" value="<?php echo ew_BtnCaption($Language->Phrase("AddBtn")) ?>">
</form>
<script type="text/javascript">
fuseradd.Init();
</script>
<?php
$user_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$user_add->Page_Terminate();
?>
