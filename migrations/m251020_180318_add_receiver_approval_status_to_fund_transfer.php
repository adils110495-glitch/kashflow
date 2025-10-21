<?php

use yii\db\Migration;

/**
 * Handles adding receiver approval status to fund_transfer table.
 */
class m251020_180318_add_receiver_approval_status_to_fund_transfer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Update existing records to use new status values
        // STATUS_PENDING = 0 (unchanged)
        // STATUS_APPROVED = 1 (unchanged) 
        // STATUS_REJECTED = 2 (unchanged)
        // STATUS_PENDING_RECEIVER_APPROVAL = 3 (new)
        // STATUS_RECEIVER_APPROVED = 4 (new)
        // STATUS_RECEIVER_REJECTED = 5 (new)
        
        // No schema changes needed, just adding new status constants
        // The existing status column can handle the new values
        
        echo "Receiver approval status constants added to FundTransfer model.\n";
        echo "New status values:\n";
        echo "- STATUS_PENDING_RECEIVER_APPROVAL = 3\n";
        echo "- STATUS_RECEIVER_APPROVED = 4\n";
        echo "- STATUS_RECEIVER_REJECTED = 5\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // No schema changes to revert
        echo "Receiver approval status constants removed from FundTransfer model.\n";
    }
}