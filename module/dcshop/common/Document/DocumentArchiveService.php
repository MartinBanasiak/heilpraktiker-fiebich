<?php
/**
 * Created by PhpStorm.
 * User: alsotohy
 * Date: 21.02.2018
 * Time: 09:41
 */

namespace DynCom\dc\dcShop\Document;


class DocumentArchiveService
{


    public function getPaginationValues($currentPage, $allRowsCount, $maxRowsNumberPerPage): array
    {
        $paginationValuesArray = array();
        if ($allRowsCount > 0) {

            if ($allRowsCount > $maxRowsNumberPerPage) {
                $rest = $allRowsCount % $maxRowsNumberPerPage;
                if ($rest != 0) {
                    $numberofSubrows = intval($allRowsCount / $maxRowsNumberPerPage) + 1;
                } else {
                    $numberofSubrows = intval($allRowsCount / $maxRowsNumberPerPage);
                }
                if (($numberofSubrows > 1) || (($numberofSubrows <= 1) && ($rest != 0))) {
                    $pages = $numberofSubrows;
                    if ($currentPage != 1) {
                        $previousPageValue = $currentPage - 1;
                    }
                    if ($currentPage != $pages) {
                        $nextPageValue = $currentPage + 1;

                    }
                }
            }
            $paginationValuesArray = [
                'current_page_value' => $currentPage,
                'number_of_pages' => $pages,
                'next_page_value' => $nextPageValue,
                'previous_page_value' => $previousPageValue,
            ];

        }
        return $paginationValuesArray;
    }


   public function sortArrayWithObjectsWithDates($array, $orderMethod)
    {
        usort($array, function ($a, $b) use ($orderMethod) {
            $a = strtotime($a->getDocumentDate());
            $b = strtotime($b->getDocumentDate());
            if ($orderMethod == "ASC") {
                return (($a === $b) ? 0 : (($a < $b) ? -1 : 1));
            } else {
                return (($a === $b) ? 0 : (($a > $b) ? -1 : 1));
            }

        });
        return $array;
    }

}