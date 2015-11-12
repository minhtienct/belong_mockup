<?php

namespace Application\Application\Constant;

/**
 * Description of CommonConstant
 *
 * @author cmtien
 */
class CommonConstant
{
    /**
     * Regex of phone number validation
     */
//    const PATTERN_PHONE_NUMBER = '/^[¥d]{3}-[¥d]{4}-[¥d]{4}$|^[¥d]{11}$/';
    const PATTERN_PHONE_NUMBER = '/^[\d-V]+$/';

    /**
     * Number of water timestamp column
     */
    const WATER_TIMESTAMP_COL_NUM = 24;

    /**
     * Number of excretion timestamp column
     */
    const EXCRETION_TIMESTAMP_COL_NUM = 24;

    /**
     * User photo size limit (byte)
     */
    const USER_PHOTO_SIZE_LIMIT = 2000000;

    /**
     * Thing image status checked
     */
    const THING_IMAGE_STATUS_CHECKED = 1;
    /**
     * Thing image status unchecked
     */
    const THING_IMAGE_STATUS_UNCHECKED = 0;
    
    /**
     * Error can not create PDF
     */
    const PDF_CAN_NOT_CREATE = 1;
    
    /**
     * Get user photo file type allowed
     * @return array
     */
    public static function getUserPhotoTypeAllowed()
    {
        return array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
    }
    
}
