<?php

include_once __DIR__ . '/../Models/FreelancerDetails.php';
class FreelancerController
{
    public static function fetchByUserID(int $userID): ?FreelancerDetails
    {
        return FreelancerDetails::FetchByUserID($userID);
    }
    public static function update(FreelancerDetails $details): bool
    {
        return FreelancerDetails::update($details);
    }
    public static function create(FreelancerDetails $details): bool
    {
        return FreelancerDetails::create($details);
    }

}
