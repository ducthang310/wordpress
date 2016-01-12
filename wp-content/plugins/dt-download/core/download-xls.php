<?php
class DT_Download_Xls
{
    private $_errorMessages = array();
    private $_content = '';
    private $_standardFields = array(
        'field_key' => array(
            'field_name' => 'field_name',
            'validate' => array(
                'not_null' => false,
                'max_length' => null,
            )
        ),
    );
    private $_exampleFields = array(
        'test_field_1' => array(
            'field_name' => 'Field Name 1',
            'validate' => array(
                'not_null' => false,
                'max_length' => null,
            )
        ),
        'test_field_2' => array(
            'field_name' => 'Field Name 2',
            'validate' => array(
                'not_null' => true,
                'max_length' => null,
            )
        ),
        'test_field_3' => array(
            'field_name' => 'Field Name 3',
            'validate' => array(
                'not_null' => false,
                'max_length' => null,
            )
        ),
        'test_field_4' => array(
            'field_name' => 'Field Name 4',
            'validate' => array(
                'not_null' => true,
                'max_length' => null,
            )
        ),
    );
    private $_exampleUserData = array(
        array("test_field_1" => "Mary", "test_field_2" => 'Johnson<?php echo "quay len ae oi"?>', "test_field_3" => '25-10-2015', 'test_field_4' => 'Trainee'),
        array("test_field_1" => "Amanda", "test_field_2" => "Miller", "test_field_3" => '23-10-1990', 'test_field_4' => 'Team leader'),
        array("test_field_1" => "James", "test_field_2" => "Brown", "test_field_3" => '13-10-2015', 'test_field_4' => 'Junior'),
        array("test_field_1" => "Patricia", "test_field_2" => "Williams", "test_field_3" => '25-04-1999', 'test_field_4' => 'Developer'),
        array("test_field_1" => "Michael", "test_field_2" => "Davis", "test_field_3" => '25-10-1985', 'test_field_4' => 'Senior'),
        array("test_field_1" => "Sarah", "test_field_2" => "Miller", "test_field_3" => '12-10-1987', 'test_field_4' => null),
        array("test_field_1" => "Johnny", "test_field_2" => "Smart", "test_field_3" => '31-10-1989', 'test_field_4' => 'Professional')
    );

    public function __construct() {
        // display field/column names as first row
        $this->_content = implode("\t", array_keys($this->_exampleFields)) . "\r\n";
    }

    private function _validateData($rowData)
    {
        $rules = $this->_exampleFields;
        foreach ($rowData as $fieldKey => $value) {
            // Will validate if the fieldKey has been declared.
            if (isset($rules[$fieldKey])) {
                // Check null
                if ($rules[$fieldKey]['validate']['is_null']) {
                    if ($value === null || $value === '') {
                        $this->_errorMessages[] = 'Some values is required. Please check again';
                    }
                }
                // Check max_length
                if ($rules[$fieldKey]['validate']['max_length']) {
                    if ($rules[$fieldKey]['validate']['max_length'] < strlen($value)) {
                        $this->_errorMessages[] = 'Some values is too long. Please check again';
                    }
                }

                // Return if An error has occurred.
                if (!empty($this->_errorMessages)) {
                    return false;
                }
            }

        }
        return true;
    }

    private function _formatData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"')) {
            $str = '"' . str_replace('"', '""', $str) . '"';
        }

        return true;
    }

    public function generateFile($data)
    {
//        $data = $this->_exampleUserData; // Use for testing without user's data
        // file name for download
        $filename = "pro_data_" . date('Ymd') . ".xls";


        foreach ($data as $row) {
            // Check error which has occurred in Validating
            if (!$this->_validateData($row)) {
                return $this->_errorMessages;
            }
            array_walk($row, array($this, '_formatData'));
            $this->_content .= implode("\t", array_values($row)) . "\n";
        }

        // Save user'data to the database
        $this->saveData($data);
        ob_end_clean();
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        echo $this->_content;
        exit;
    }

    public function saveData($data) {
        global $wpdb, $dtdf_table;

        foreach ($data as $row) {
            $wpdb->insert( $dtdf_table, $row);
        }

        return true;
    }

    public function createForm() {
        $formHtml = '<form  action="' . get_permalink() . '" method="POST" class="form-horizontal" id="dt_download_xls_form" role="form">';
        $field_html = '';
        $requireHtml = '';
        $submitHtml = '
            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    <button type="submit" class="btn btn-primary">Download File</button>
                </div>
            </div>
        ';

        if (!empty($this->_errorMessages)) {
            foreach($this->_errorMessages as $message) {
                $formHtml .= '
                    <div class="alert alert-danger fade in">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <strong>Error!</strong> ' . $message . '
                    </div>
                ';
            }
        }

        $fields = $this->_exampleFields;

        foreach ($fields as $fieldKey => $field) {
            if ($field['validate']['not_null']) {
                $requireHtml = 'required="true"';
            }
            $field_html .= '<div class="form-group">';
            $field_html .= '<label for="' . $fieldKey . '"  class="control-label col-xs-2">' . $field['field_name']. '</label>';
            $field_html .= '<div class="col-xs-10">';
            $field_html .= '<input type="text" class="form-control" id="' . $fieldKey . '" name="g_data[' . $fieldKey . ']" placeholder="' . $field['field_name']. '" ' . $requireHtml . '/>';
            $field_html .= '</div>';
            $field_html .= '</div>';
        }

        $formHtml .= $field_html . $submitHtml . '</form>';

        return $formHtml;
    }
}

global $dtdf_xls;

$dtdf_xls = new DT_Download_Xls();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['g_data'])) {
    $dtdf_xls->generateFile(array($_POST['g_data']));
}