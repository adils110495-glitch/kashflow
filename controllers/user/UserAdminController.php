<?php
namespace app\controllers\user;

use dektrium\user\controllers\AdminController as BaseAdminController;
use dektrium\user\models\User;
use Yii;

class UserAdminController extends BaseAdminController
{
    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var User $user */
        $user = \Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);

        $this->performAjaxValidation($user);

        if ($user->load(\Yii::$app->request->post())) {
            // Automatically set username to email for non-customer users
            if (!empty($user->email)) {
                $user->username = $user->email;
            }
            
            if ($user->create()) {
                \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been created'));
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'user' => $user
        ]);
    }
}