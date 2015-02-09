<?php

namespace Backend\Modules\Forum\Engine;

use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * Int his file we'll put some helper functions.
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class Helper
{
    public function getProfileProperty($id, $property) {

        $return = '';

        if(BackendProfilesModel::exists($id)) {

            // get profile
            $profile = BackendProfilesModel::get($id);

            // return required property
            if (!empty($profile) && isset($profile[$property])) {
                $return = $profile[$property];
            }
        }

        return $return;
    }
}