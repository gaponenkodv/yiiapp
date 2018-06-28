<?php
/**
 * Created by PhpStorm.
 * User: gaponenko
 * Date: 28.06.2018
 * Time: 13:30
 */

namespace app\controllers;

use app\models\ChangeBalance;
use app\job\ListenInvoices;
use Yii;
use yii\base\Controller;

class BalanceController extends Controller
{

    /**
     * @return string
     * @throws \Exception
     */
    public function actionChange()
    {
        $form_model = new ChangeBalance();

        if ($form_model->load(Yii::$app->request->post()) && $form_model->validate())
        {
            try
            {
                return $this->render(
                    'result',
                    [
                        'message' => $form_model->changeBalance()
                            ? 'Счет создан, ожидайте платы'
                            : 'Неизвестная ошибка выставления счета'
                    ]);
            }
            catch (\Exception $e)
            {
                return $this->render(
                    'result',
                    [
                        'message' => $e->getMessage()
                    ]);
            }
        }

        return $this->render('change', compact('form_model'));
    }

    public function actionRun()
    {
        Yii::$app->queue->push(new ListenInvoices());
    }
}