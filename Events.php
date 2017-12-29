<?php

namespace humhub\modules\announcements;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\announcements\models\Announcement;
use humhub\modules\announcements\models\AnnouncementUser;
use Yii;
use humhub\modules\space\models\Membership;

/**
 * Description of Events
 *
 * @author davidborn
 */
class Events extends \yii\base\Object
{

    public static function onWallEntryControlsInit($event)
    {
        $object = $event->sender->object;

        if(!$object instanceof Announcement) {
            return;
        }

        if($object->content->canEdit()) {
            $event->sender->addWidget(\humhub\modules\announcements\widgets\CloseButton::className(), [
                'announcement' => $object
            ]);
        }

        if($object->isResetAllowed()) {
            $event->sender->addWidget(\humhub\modules\announcements\widgets\ResetButton::className(), [
                'announcement' => $object
            ]);
        }
    }
    
    public static function onMemberAdded ($event)
    {
        // add member to open announcements
        $announcements = Announcement::find()->contentContainer($event->space)->all();
        if (isset($announcements) && $announcements !== null) {
            foreach ($announcements as $announcement) {
                if ($announcement->closed)
                    continue;
                $announcement->setConfirmation($event->user);
            }
        }

//        echo '<pre>Space: ';
//        print_r($event->space->name);
//        echo '</pre>';
//        echo '<pre>User: ';
//        print_r($event->user->username);
//        echo '</pre>';
//        die();
    }

    public static function onMemberRemoved ($event)
    {
        // TODO: remove member from  announcements
        $announcements = Announcement::find()->contentContainer($event->space)->all();
        if (isset($announcements) && $announcements !== null) {
            foreach ($announcements as $announcement) {
                if ($announcement->closed)  // skip closed announcements, because we want user to be part of statistics
                    continue;
                $announcementUser = $announcement->findAnnouncementUser($event->user);
                if (isset($announcementUser) && $announcementUser !== null) {
                    $announcement->unlink('confirmations', $announcementUser, true);
                }
            }
        }
//        echo '<pre>Space: ';
//        print_r($event->space->name);
//        echo '</pre>';
//        echo '<pre>User: ';
//        print_r($event->user->username);
//        echo '</pre>';
//        die();
    }


    /**
     * On build of a Space Navigation, check if this module is enabled.
     * When enabled add a menu item
     *
     * @param type $event
     */
    public static function onSpaceMenuInit($event)
    {
        $space = $event->sender->space;

        // Is Module enabled on this workspace?
        if ($space->isModuleEnabled('announcements')) {
            $event->sender->addItem(array(
                'label' => Yii::t('AnnouncementsModule.base', 'Announcements'),
                'group' => 'modules',
                'url' => $space->createUrl('/announcements/announcement/show'),
                'icon' => '<i class="fa fa-bullhorn"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'announcements'),
            ));
        }
    }

    /**
     * On User delete, delete all announcements connected to this user
     *
     * @param type $event
     */
    public static function onUserDelete($event)
    {
        foreach (AnnouncementUser::findAll(array('user_id' => $event->sender->id)) as $user) {
            $user->delete();
        }

        return true;
    }

    /**
     * Callback to validate module database records.
     *
     * @param Event $event
     */
//    public static function onIntegrityCheck($event)
//    {
//        $integrityController = $event->sender;
//        $integrityController->showTestHeadline("Polls Module - Answers (" . PollAnswer::find()->count() . " entries)");
//        foreach (PollAnswer::find()->joinWith('poll')->all() as $answer) {
//            if ($answer->poll === null) {
//                if ($integrityController->showFix("Deleting poll answer id " . $answer->id . " without existing poll!")) {
//                    $answer->delete();
//                }
//            }
//        }
//
//        $integrityController->showTestHeadline("Polls Module - Answers User (" . PollAnswerUser::find()->count() . " entries)");
//        foreach (PollAnswerUser::find()->joinWith(['poll', 'user'])->all() as $answerUser) {
//            if ($answerUser->poll === null) {
//                if ($integrityController->showFix("Deleting poll answer id " . $answerUser->id . " without existing poll!")) {
//                    $answerUser->delete();
//                }
//            }
//            if ($answerUser->user === null) {
//                if ($integrityController->showFix("Deleting poll answer id " . $answerUser->id . " without existing user!")) {
//                    $answerUser->delete();
//                }
//            }
//        }
//    }


}

