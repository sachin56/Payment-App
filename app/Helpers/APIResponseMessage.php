<?php

namespace App\Helpers;

class APIResponseMessage
{

    const ERROR_STATUS = "false";
    const SUCCESS_STATUS = "true";
    const ROUTE_NOT_FOUND = "The route you are trying to access not exists.";
    const UNAUTHORIZED = "Unauthorized. Access is denied due to invalid credentials.";
    const MODEL_NOT_FOUND = "Entity you are looking for not exists.";
    const HTTP_NOT_FOUND = "The route you are trying to access not exists.";
    const EMAIL_NOT_SEND = "Sorry! Please try again latter.";
    const PW_RESET_SUCCESS = "Password was successfully reset.";
    const INVALID_OR_INACTIVE_USER_FORGET_PW = "User In-active or invalid Email Address.";
    const PW_RESET_EMAIL_SUCCESS = "Password reset email sent.";
    const USER_LOCKED = "User Account Locked. Please Contact Administrator, Thank you.";
    const USER_DELETED = "User Account Deleted.";
    const INVALID_USER_NAME_PASSWORD = "Invalid email address.";
    const INVALID_EMAIL = "Invalid email address.";
    const NO_DATA = 'Data fetched failed';
    const NO_DATA_FOUND = 'No data found near your location.';
    const DATA_FOUND = 'Data found';
    const UPDATED = 'Successfully updated';
    const UPLOADED = 'Successfully uploaded';
    const UPLOAD_FAILED = 'Image upload failed';
    const ERROR_EXCEPTION = 'Failed the process';
    const DELETED = 'Successfully deleted';
    const RESTORE = 'Successfully restore';
    const CREATED = 'Successfully Saved';
    const FAIL = 'Record Create Fail';
    const DISPATCHED = 'Successfully Dispatched';
    const PAYMENT_FAILED = 'Payment Failed';
    const EMAIL_SENT = "Email send successfully.";
    const LOGIN_SUCCESS = "Login successful.";
    const UNAUTHORIZED_ROLE = "Unauthorized. Access is denied due to role permission.";
    const UNAUTHORIZED_USER = "Unauthorized. Access.";
    const DEACTIVALE_RECORD = "Record deactivate successfully.";
    const ACTIVATE_RECORD = "Record activate successfully.";
    const RETRIVED_SUCCESSFULLY = "Data retrieved successfully";
    const NEAREST_PLACE = "Nearest place retrieved successfully.";
    const CODEVALID = "Password code is valid.";
    const CODEVINALID = "Password code is invalid.";
    const CODEEXPIRE = "code is expired.";
    const DATAFETCHEDFAILED = "Data fetched failed.";
    const DATAFETCHED = "Data fetched.";
    const NODATA = "no data found.";


}
