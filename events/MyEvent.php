<?php

namespace app\events;
use Yii;

/**
 * Класс какого то события
 *
 * @package app\events
 */
class MyEvent
{
    /**
     * Обработчик какого то события
     */
    public function loadPaymentEventHendler()
    {
        Yii::debug('Платеж загружен');
    }
}