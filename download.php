<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the m3u8 URL from the form submission
    $m3u8_url = $_POST["m3u8_url"];

    // Validate the URL (you might want to add more thorough validation)
    if (!filter_var($m3u8_url, FILTER_VALIDATE_URL)) {
        echo "Invalid URL.";
        exit();
    }

    // Output directory where the segment will be saved
    $output_directory = "OUTPUT_DIRECTORY_PATH_HERE";

    // Command to execute using ffmpeg
    $ffmpeg_command = "ffmpeg -i $m3u8_url -c copy $output_directory/output.ts";

    // Execute the command
    $output = shell_exec($ffmpeg_command);

    // Check if the segment was captured successfully
    if (file_exists("$output_directory/output.ts")) {
        // Set headers for file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename("$output_directory/output.ts") . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize("$output_directory/output.ts"));
        // Flush buffer
        ob_clean();
        flush();
        // Read the file and output it to the browser
        readfile("$output_directory/output.ts");
        // Delete the file after download
        unlink("$output_directory/output.ts");
        exit();
    } else {
        echo "Failed to capture segment.";
    }
}

?>
