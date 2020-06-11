<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_kuracloud\privacy;
defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;


class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    public static function get_metadata(collection $collection) : collection {
        // Describe the data we are storing locally
        $collection->add_database_table(
            'block_kuracloud_users',
            [
                'userid' => 'privacy:metadata:block_kuracloud_users:userid',
                'remote_studentid' => 'privacy:metadata:block_kuracloud_users:remote_studentid',

            ],
            'privacy:metadata:block_kuracloud_users'
        );

        // Describe the data exported to kuraCloud
        $collection->add_external_location_link('kuracloud_sync', [
            'firstname' => 'privacy:metadata:kuracloud_sync:firstname',
            'lastname' => 'privacy:metadata:kuracloud_sync:lastname',
            'idnumber' => 'privacy:metadata:kuracloud_sync:idnumber',
            'email' => 'privacy:metadata:kuracloud_sync:email',
        ], 'privacy:metadata:kuracloud_sync');

        return $collection;
    }


    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        if ($DB->record_exists('block_kuracloud_users', ['userid' => $userid])) {
            // $contextlist->add_system_context();
            $contextlist->add_user_context($userid);
            // Look up course context????
        }
        return $contextlist;

    }


    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (!$context instanceof \context_system) {
            return;
        }

        // add_users, get users direct or do SQL query? add_from_sql is probably better since using the
        // $params = [];
        // $sql = "SELECT u.userid
        //     FROM {block_kuracloud_users} u;
        // $userlist->add_from_sql('userid', $sql, $params);
}


    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        // Test context ????

        // https://wimski.org/api/3.8/d7/d9c/classcore__privacy_1_1local_1_1request_1_1writer.html
        // writer::with_context returns content_writer

        // https://wimski.org/api/3.8/dd/d21/interfacecore__privacy_1_1local_1_1request_1_1content__writer.html
        // export_data(array $subcontext, stdClass $data)
        // export_metadataarray(array $subcontext, string $name, $value, string	$description)

        // foreach ($contextlist->get_contexts() as $context) {
            // join over block_kuracloud_users and block_kuracloud_courses to map from course to user?
            // $records = $DB->get_records('block_kuracloud_users', array('userid' => $user->id));
            // writer::with_context(context_user::instance($user->id))->export_data([], $records);
            // writer::with_context(context_system)->export_data([], $records);
        // }
        $dummy = (object) [
            'kura_name' => format_string("foo bar", true)
        ];
        // writer::with_context(context_system)->export_data([], $dummy);
        writer::with_context(context_user::instance($user->id))->export_data([], $dummy);
    }


    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {

    }


    public static function delete_data_for_user(approved_contextlist $contextlist) {

    }


    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {

    }
}
