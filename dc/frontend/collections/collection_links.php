<div class="collection collection<?= $code ?>">
    <div class="row flexrow">
        <?
        foreach ($collections as $collection_id => $collectionData) {
            $collectionFullLines = array();
            foreach ($setup_fields as $fieldId => $fieldData) {
                if (!isset($collection_lines[$collection_id])) {
                    continue;
                }

                if (!isset($collection_lines[$collection_id][$fieldId])) {
                    continue;
                }

                if ($fullView === FALSE && $fieldData['is_teaser'] == 0) {
                    continue;
                }

                $collection_lines[$collection_id][$fieldId]['code'] = $fieldData['code'];
                $collection_lines[$collection_id][$fieldId]['fieldname'] = $fieldData['fieldname'];
                $collection_lines[$collection_id][$fieldId]['type_id'] = $fieldData['type_id'];
                $collection_lines[$collection_id][$fieldId]['fieldtype'] = $fieldData['fieldtype'];

                $collectionFullLines[$fieldData['code']] = $collection_lines[$collection_id][$fieldId];
            }
            include('collection_' . $code . '_list.php');
        } ?>
    </div>
</div>
