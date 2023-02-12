<?php
  //My own semiphores for tracking
  $local = true;
  //Console Log PHP Emulator
  function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log('.json_encode($output, JSON_HEX_TAG) .');';
    if ($with_script_tags) {
        $js_code = '<script>'. $js_code .'</script>';
    }
    echo $js_code;
  }
  // Initialize the session
  session_start();
?>