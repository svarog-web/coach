<?php
/**
* Community Builder (TM) cbgroupjivefile Greek (Greece) language file Frontend
* @version $Id:$
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* WARNING:
* Do not make changes to this file as it will be over-written when you upgrade CB.
* To localize you need to create your own CB language plugin and make changes there.
*/

defined('CBLIB') or die();

return	array(
// 16 language strings from file cbgroupjivefile/cbgroupjivefile.xml
'INPUT_NUMBER_OF_FILES_EACH_INDIVIDUAL_USER_IS_LIMI_db8dbc'	=>	'Εισάγετε το όριο στην ποσότητα των αρχείων που μπορεί ο κάθε μεμονωμένος χρήστης να δημιουργήσει ανά ομάδα. Εάν το αφήσετε κενό δεν θα υπάρχει όριο. Οι Συντονιστές και οι ιδιοκτήτες της ομάδας εξαιρούνται αυτής της ρύθμισης.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_FILES__cf15ea'	=>	'Ενεργοποίηση ή απενεργοποίηση του captcha στα αρχεία των ομάδων. Για να λειτουργήσει απαιτείται η εγκατάσταση και δημοσίευση του πιο πρόσφατου CB AntiSpam. Οι διαχειριστές εξαιρούνται από αυτή την ρύθμιση.',
'INPUT_THE_MINIMUM_FILE_SIZE_IN_KBS_f6c682'	=>	'Εισάγετε το ελάχιστο μέγεθος αρχείου σε KBs.',
'INPUT_THE_MAXIMUM_FILE_SIZE_IN_KBS_SET_TO_0_FOR_NO_58cb50'	=>	'Εισάγετε το μέγιστο μέγεθος αρχείου σε KBs. Συμπληρώστε 0 για να μην υπάρχει όριο.',
'INPUT_THE_ALLOWED_FILE_EXTENSIONS_AS_A_COMMA_SEPAR_75447c'	=>	'Εισάγετε τις επιτρεπόμενες επεκτάσεις αρχείων διαχωρισμένες με κόμμα.',
'FILE_TYPES_f12b42'	=>	'Τύποι Αρχείων',
'UPLOAD_OF_NEW_FILE_6e6e69'	=>	'Φόρτωση νέου αρχείου',
'NEW_FILE_REQUIRES_APPROVAL_cef783'	=>	' Το νέο αρχείο χρειάζεται έγκριση',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Ενεργοποίηση ή απενεργοποίηση χρήσης σελιδοποίησης.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Εισάγετε όριο σελίδων. Αυτό καθορίζει το πόσες γραμμές θα εμφανίζονται ανά σελίδα. Εάν η σελιδοποίηση είναι απενεργοποιημένη αυτό μπορεί να χρησιμοποιηθεί για να περιορίσει τον αριθμό γραμμών που εμφανίζονται.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Ενεργοποίηση ή απενεργοποίηση χρήσης αναζήτησης στις γραμμές.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Επιλέξτε την σειρά των γραμμών.',
'DATE_ASC_a5871f'	=>	'Ημερομηνία σε Αύξουσα',
'DATE_DESC_bcfc6d'	=>	'Ημερομηνία σε Φθίνουσα',
'FILENAME_ASC_44f721'	=>	'Όνομα Αρχείου σε Αύξουσα',
'FILENAME_DESC_13d728'	=>	'Όνομα Αρχείου σε Φθίνουσα',
// 31 language strings from file cbgroupjivefile/component.cbgroupjivefile.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Η ομάδα δεν υπάρχει.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_050e48'	=>	'Δεν έχετε επαρκή άδεια για να φορτώσετε αρχείο σε αυτή την ομάδα.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_338361'	=>	'Δεν έχετε επαρκή άδεια για να επεξεργαστείτε αυτό το αρχείο.',
'SELECT_PUBLISH_STATE_OF_THIS_FILE_UNPUBLISHED_FILE_4be1f3'	=>	'Επιλέξτε την κατάσταση δημοσίευσης αυτού του αρχείου. Μη δημοσιευμένα αρχεία δεν είναι ορατά στο κοινό.',
'OPTIONALLY_INPUT_A_FILE_TITLE_TO_DISPLAY_INSTEAD_O_b6523c'	=>	'Προαιρετικά εισάγετε ένα τίτλο που θα εμφανίζεται αντί για το όνομα του αρχείου.',
'SELECT_THE_FILE_TO_UPLOAD_739b2a'	=>	'Επιλέξτε αρχείο για φόρτωση.',
'GROUP_FILE_LIMITS_EXT'	=>	'Το αρχείο σας πρέπει να είναι τύπου [ext].',
'GROUP_FILE_LIMITS_MIN'	=>	'Το αρχείο σας πρέπει να υπερβαίνει τα [size].',
'GROUP_FILE_LIMITS_MAX'	=>	'Το αρχείο σας δεν πρέπει να υπερβαίνει τα [size].',
'OPTIONALLY_INPUT_A_FILE_DESCRIPTION_b5bf92'	=>	'Προαιρετικά εισάγετε περιγραφή για το αρχείο.',
'INPUT_THE_FILE_OWNER_ID_FILE_OWNER_DETERMINES_THE__8773c0'	=>	'Εισάγετε το id του ιδιοκτήτη του αρχείου. Αυτό καθορίζει τον δημιουργό του αρχείου με μορφή ID Χρήστη.',
'GROUP_FILE_FAILED_TO_SAVE'	=>	'Η αποθήκευση του αρχείου απέτυχε! Σφάλμα: [error] ',
'GROUP_FILE_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_FILE_35b542'	=>	'Νέο αρχείο ομάδας',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_db49b7'	=>	'[user] φόρτωσε το αρχείο [file] στην ομάδα [group]!',
'NEW_GROUP_FILE_AWAITING_APPROVAL_498d56'	=>	'Νέο αρχείο ομάδας περιμένει έγκριση',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_37493d'	=>	'[user] φόρτωσε το αρχείο [file] στην ομάδα [group] και περιμένει έγκριση!',
'FILE_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_1e465f'	=>	'Το αρχείο φορτώθηκε με επιτυχία και περιμένει έγκριση!',
'FILE_UPLOADED_SUCCESSFULLY_2169cb'	=>	'Το αρχείο φορτώθηκε επιτυχώς!',
'FILE_SAVED_SUCCESSFULLY_c80b21'	=>	'Το αρχείο αποθηκεύτηκε επιτυχώς!',
'YOUR_FILE_IS_AWAITING_APPROVAL_dabe41'	=>	'Το αρχείο σας περιμένει έγκριση.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__fbef44'	=>	'Δεν έχετε επαρκή άδεια για να δημοσιεύσετε ή όχι αυτό το αρχείο.',
'FILE_DOES_NOT_EXIST_50db35'	=>	'Το αρχείο δεν υπάρχει.',
'GROUP_FILE_STATE_FAILED_TO_SAVE'	=>	'Η κατάσταση του αρχείου δεν αποθηκεύτηκε. Σφάλμα: [error]',
'FILE_UPLOAD_REQUEST_ACCEPTED_623624'	=>	'Το αίτημα φόρτωσης αρχείου εγκρίθηκε',
'YOUR_FILE_FILE_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_H_80bbf8'	=>	'Το αίτημα σας για φόρτωση του αρχείου  [file] uστην ομάδα [group] έγινε δεκτό!',
'FILE_STATE_SAVED_SUCCESSFULLY_cc3caf'	=>	'Η κατάσταση του αρχείου αποθηκεύτηκε επιτυχώς!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_35a6cc'	=>	'Δεν έχετε επαρκή άδεια για να διαγράψετε αυτό το αρχείο.',
'GROUP_FILE_FAILED_TO_DELETE'	=>	'Η διαγραφή του αρχείου απέτυχε! Σφάλμα: [error] ',
'FILE_DELETED_SUCCESSFULLY_5ea0ed'	=>	'Το αρχείο διαφράφτηκε επιτυχώς!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_FILE_d7c056'	=>	'Δεν έχετε πρόσβαση σε αυτό το αρχείο.',
// 8 language strings from file cbgroupjivefile/library/Table/FileTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Δεν καθορίστηκε ιδιοκτήτης!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Δεν καθορίστηκε ομάδα!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Η ομάδα δεν υπάρχει!',
'GROUP_FILE_UPLOAD_INVALID_EXT'	=>	'Άκυρη επέκταση αρχείου [ext]. Παρακαλώ φορτώστε μόνο [exts]!',
'GROUP_FILE_UPLOAD_TOO_SMALL'	=>	'Το αρχείο είναι πολύ μικρό, το ελάχιστο μέγεθος είναι [size]!',
'GROUP_FILE_UPLOAD_TOO_LARGE'	=>	'Το αρχείο είναι πολύ μεγάλο, το μέγιστο μέγεθος είναι [size]!',
'FILE_NOT_SPECIFIED_93ec32'	=>	'Δεν καθορίστηκε αρχείο!',
'GROUP_FILE_UPLOAD_FAILED'	=>	'Η φόρτωση του αρχείου [file] απέτυχε!',
// 3 language strings from file cbgroupjivefile/library/Trigger/AdminTrigger.php
'FILES_91f3a2'	=>	'Αρχεία',
'ADD_NEW_FILE_TO_GROUP_518801'	=>	'Εισαγωγή Νέου Αρχείου σε Ομάδα',
'CONFIGURATION_254f64'	=>	'Διαμόρφωση',
// 6 language strings from file cbgroupjivefile/library/Trigger/FileTrigger.php
'DISABLE_bcfacc'	=>	'Απενεργοποίηση ',
'ENABLE_2faec1'	=>	'Ενεργοποίηση',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Ενεργοποίηση με Έγκριση',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_FILES_GROUP__3216b3'	=>	'Προαιρετικά ενεργοποιήστε ή απενεργοποιήστε την χρήση αρχείων. Ο ιδιοκτήτης της ομάδας καθώς και οι Διαχειριστές εξαιρούνται από αυτή τη ρύθμιση και μπορούν πάντα να φορτώνουν αρχεία. Σημειώστε ότι τα υπάρχοντα αρχεία θα είναι πάντα διαθέσιμα.',
'DONT_NOTIFY_3ea23f'	=>	'Χωρίς Ειδοποίηση',
'SEARCH_FILES_ec9e5b'	=>	'Αναζήτηση Αρχείων...',
// 5 language strings from file cbgroupjivefile/templates/default/activity.php
'GROUP_FILE_ACTIVITY_TITLE'	=>	'φόρτωση αρχείου σε [group]',
'TYPE_a1fa27'	=>	'Τύπος',
'SIZE_6f6cb7'	=>	'Μέγεθος',
'CLICK_TO_DOWNLOAD_26f519'	=>	'Κάνετε κλικ για να Κατεβάσετε',
'UNKNOWN_88183b'	=>	'Άγνωστο',
// 6 language strings from file cbgroupjivefile/templates/default/file_edit.php
'EDIT_FILE_29e095'	=>	'Επεξεργασία Αρχείου',
'NEW_FILE_10716b'	=>	'Νέο Αρχείο',
'FILE_0b2791'	=>	'Αρχείο',
'DESCRIPTION_b5a7ad'	=>	'Περιγραφή',
'UPDATE_FILE_e9812b'	=>	'Ενημερωμένο Αρχείο',
'UPLOAD_FILE_fbb7d7'	=>	'Φόρτωση Αρχείου',
// 7 language strings from file cbgroupjivefile/templates/default/files.php
'GROUP_FILES_COUNT'	=>	'%%COUNT%% Αρχείο|%%COUNT%% Αρχείων',
'AWAITING_APPROVAL_af6558'	=>	'Περιμένει Έγκριση',
'APPROVE_6f7351'	=>	'Έγκριση',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_FILE_babc72'	=>	'Σίγουρα θέλετε να αναιρέσετε την δημοσίευση αυτού του Αρχείου;',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_FILE_494de2'	=>	'Σίγουρα θέλετε την διαγραφή αυτού του Αρχείου;',
'NO_GROUP_FILE_SEARCH_RESULTS_FOUND_6609b5'	=>	'Δεν βρέθηκε αποτέλεσμα στην αναζήτηση αρχείων της ομάδας.',
'THIS_GROUP_CURRENTLY_HAS_NO_FILES_f0b8c6'	=>	'Αυτή η ομάδα δεν έχει αρχεία προς το παρόν.',
);
