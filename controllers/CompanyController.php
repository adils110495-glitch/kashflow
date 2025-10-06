<?php

namespace app\controllers;

use Yii;
use app\models\Company;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Shows company form - create if no company exists, update if company exists.
     * @return mixed
     */
    public function actionIndex()
    {
        $company = Company::find()->one();
        
        if ($company) {
            // Company exists, show update form
            return $this->redirect(['update', 'id' => $company->id]);
        } else {
            // No company exists, show create form
            return $this->redirect(['create']);
        }
    }

    /**
     * Displays a single Company model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Company();

        if ($model->load(Yii::$app->request->post())) {
            // Handle logo upload
            $logoFile = UploadedFile::getInstance($model, 'logo');
            if ($logoFile) {
                $logoPath = 'uploads/company/' . time() . '_' . $logoFile->baseName . '.' . $logoFile->extension;
                if ($logoFile->saveAs($logoPath)) {
                    $model->logo = $logoPath;
                }
            }
            
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldLogo = $model->logo;

        if ($model->load(Yii::$app->request->post())) {
            // Handle logo upload
            $logoFile = UploadedFile::getInstance($model, 'logo');
            if ($logoFile) {
                $logoPath = 'uploads/company/' . time() . '_' . $logoFile->baseName . '.' . $logoFile->extension;
                if ($logoFile->saveAs($logoPath)) {
                    // Delete old logo if exists
                    if ($oldLogo && file_exists($oldLogo)) {
                        unlink($oldLogo);
                    }
                    $model->logo = $logoPath;
                } else {
                    $model->logo = $oldLogo;
                }
            } else {
                $model->logo = $oldLogo;
            }
            
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete logo file if exists
        if ($model->logo && file_exists($model->logo)) {
            unlink($model->logo);
        }
        
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}