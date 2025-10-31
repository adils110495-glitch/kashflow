<?php

namespace app\controllers;

use app\models\RoiPlan;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * RoiPlanController implements the CRUD actions for RoiPlan model.
 */
class RoiPlanController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all RoiPlan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => RoiPlan::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RoiPlan model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RoiPlan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new RoiPlan();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RoiPlan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing RoiPlan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * ROI Plan Configuration Form with ROI and Referral tabs
     * @return string|\yii\web\Response
     */
    public function actionConfigure()
    {
        // Load ROI data from options table
        $roiData = [
            'rate' => \app\models\Options::getValue('roi_rate', ''),
            'frequency' => \app\models\Options::getValue('roi_frequency', ''),
            'tenure' => \app\models\Options::getValue('roi_tenure', ''),
            'status' => \app\models\Options::getValue('roi_status', ''),
        ];

        // Load referral extra bonus data from options table
        $referralExtraBonusData = [
            'no_of_referral' => \app\models\Options::getValue('referral_extra_bonus_no_of_referral', ''),
            'rate' => \app\models\Options::getValue('referral_extra_bonus_rate', ''),
            'frequency' => \app\models\Options::getValue('referral_extra_bonus_frequency', ''),
            'tenure' => \app\models\Options::getValue('referral_extra_bonus_tenure', ''),
        ];

        // Load referral bonus (single rate) data
        $referralBonusData = [
            'rate' => \app\models\Options::getValue('referral_bonus_rate', ''),
        ];

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            // Handle ROI Plan submission
            if (isset($postData['roi'])) {
                $roiData = $postData['roi'];
                
                // Save ROI data to options table
                \app\models\Options::setValue('roi_rate', $roiData['rate']);
                \app\models\Options::setValue('roi_frequency', $roiData['frequency']);
                \app\models\Options::setValue('roi_tenure', $roiData['tenure']);
                \app\models\Options::setValue('roi_status', $roiData['status']);
                
                Yii::$app->session->setFlash('success', 'ROI Plan saved successfully.');
                return $this->redirect(['configure']);
            }
            
            // Handle Referral Extra Bonus submission
            if (isset($postData['referral_extra_bonus'])) {
                $referralExtraBonusData = $postData['referral_extra_bonus'];

                // Save referral extra bonus data to options table
                \app\models\Options::setValue('referral_extra_bonus_no_of_referral', $referralExtraBonusData['no_of_referral']);
                \app\models\Options::setValue('referral_extra_bonus_rate', $referralExtraBonusData['rate']);
                \app\models\Options::setValue('referral_extra_bonus_frequency', $referralExtraBonusData['frequency']);
                \app\models\Options::setValue('referral_extra_bonus_tenure', $referralExtraBonusData['tenure']);

                Yii::$app->session->setFlash('success', 'Referral Extra Bonus saved successfully.');
                return $this->redirect(['configure']);
            }

            // Handle Referral Bonus submission (single rate)
            if (isset($postData['referral_bonus'])) {
                $referralBonusData = $postData['referral_bonus'];

                \app\models\Options::setValue('referral_bonus_rate', $referralBonusData['rate']);

                Yii::$app->session->setFlash('success', 'Referral Bonus saved successfully.');
                return $this->redirect(['configure']);
            }
        }

        return $this->render('configure', [
            'roiData' => $roiData,
            'referralExtraBonusData' => $referralExtraBonusData,
            'referralBonusData' => $referralBonusData,
        ]);
    }

    /**
     * Finds the RoiPlan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return RoiPlan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RoiPlan::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
