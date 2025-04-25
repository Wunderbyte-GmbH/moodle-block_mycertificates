<?php
require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$certid = required_param('certid', PARAM_ALPHANUMEXT);

require_login(0, false); // Not logged in is OK (public)

// Check if the certificate exists in the database (adjust for your cert plugin/table)
global $DB;

$course = $DB->get_record('course', ['id' => $courseid], '*', IGNORE_MISSING);
if (!$course) {
    throw new moodle_exception('invalidcourseid', 'error');
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/certpreview/index.php', [
    'courseid' => $courseid,
    'certid' => $certid,
]));
$PAGE->set_title("Course Certificate Preview");
$PAGE->set_heading("Certificate Preview");
$PAGE->set_pagelayout('popup'); // or 'standard' if you want full page
$PAGE->add_head_tag('meta', ['property' => 'og:title', 'content' => "Certificate for " . format_string($course->fullname)]);
$PAGE->add_head_tag('meta', ['property' => 'og:type', 'content' => 'website']);
$PAGE->add_head_tag('meta', ['property' => 'og:url', 'content' => $PAGE->url->out(false)]);
$PAGE->add_head_tag('meta', ['property' => 'og:description', 'content' => "Preview of the certificate for the course: " . format_string($course->fullname)]);
$PAGE->add_head_tag('meta', ['property' => 'og:image', 'content' => $CFG->wwwroot . '/blocks/mycertificates/pix/default_cert_image.png']); // customize path

echo $OUTPUT->header();

// Render template
$templatecontext = [
    'coursetitle' => format_string($course->fullname),
    'certificateid' => $certid
];

echo $OUTPUT->render_from_template('local_certpreview/certificate', $templatecontext);
echo $OUTPUT->footer();
