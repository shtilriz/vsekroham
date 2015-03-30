<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Type as FieldType;

$arReturn = array("STATUS" => '', "MESSAGE" => '');

$reviev_id = clearInt($_GET["reviev_id"]);
$vote = clearStr($_GET["vote"]);

if ($reviev_id && in_array($vote, array("Y", "N")) && CModule::IncludeModule("highloadblock")) {
    $bExistVoting = false; //флаг разрешено ли голосовать
    $arReviewVote = array();
    if (isset($_COOKIE['review_vote'])) {
        $arReviewVote = unserialize(base64_decode($_COOKIE['review_vote']));
        //если в массиве отзывовов нет данного id, значит можно проголосовать
        if (!in_array($reviev_id, $arReviewVote)) {
            $bExistVoting = true;
        }
        else {
            $arReturn["STATUS"] = "ERROR";
            $arReturn["MESSAGE"] = "Вы уже проголосовали за данный отзыв.";
        }
    }
    else {
        //если кук вообще нет, то разрешается проголосовать
        $bExistVoting = true;
    }
    if ($bExistVoting) {
        //достать количество за и против
        $hlblock = HL\HighloadBlockTable::getById(6)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $rsData = $entity_data_class::getList(array(
            "select" => array("UF_LIKE", "UF_DIZLIKE"),
            "order" => array(),
            "filter" => array("ID" => $reviev_id)
        ));
        if ($arReview = $rsData->Fetch()) {
            $like = clearInt($arReview["UF_LIKE"]);
            $dizlike = clearInt($arReview["UF_DIZLIKE"]);
            if ($vote == 'Y')
                $like++;
            elseif ($vote == 'N')
                $dizlike++;
            $arFields = array(
                "UF_LIKE" => $like,
                "UF_DIZLIKE" => $dizlike
            );
            $result = $entity_data_class::update($reviev_id, $arFields);
            if ($result->isSuccess()) {
                $arReturn["STATUS"] = "OK";
                $arReturn["MESSAGE"] = "Спасибо! Ваш голос учтен.";
                $arReturn = array_merge($arReturn, $arFields);
                //поставить куку, что пользователь уже проголосовал за данный отзыв.
                $arReviewVote[] = $reviev_id;
                setcookie('review_vote', base64_encode(serialize($arReviewVote)), 0x7FFFFFFF, "/");
            }
            else {
                $arReturn["STATUS"] = "ERROR";
                $arReturn["MESSAGE"] = "Возникла ошибка при голосовании. Повторите попытку позже.";
            }
        }
    }
}
else {
    $arReturn["STATUS"] = "ERROR";
    $arReturn["MESSAGE"] = "Возникла ошибка при голосовании. Повторите попытку позже.";
}
echo json_encode($arReturn);
?>
