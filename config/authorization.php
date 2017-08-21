<?php
/**
 * Returns the configuration for the AuthorizationInterface adapter
 *
 * Example using ZendAcl:
 *
 * 'roles' => [
 *     // insert the role with parent (if any)
 *     // e.g. 'editor' => ['admin'] (admin is parent of editor)
 * ],
 * 'resources' => [
 *     // an array of resources, as string
 * ],
 * 'allow' => [
 *     // for each role allow some resources
 *     // e.g. 'admin' => ['admin.pages']
 * ],
 * 'deny' => [
 *     // for each role deny some resources
 *     // e.g. 'admin' => ['admin.pages']
 * ]
 *
 * Example using ZendRbac:
 *
 * 'roles' => [
 *     // insert the role with parent (if any)
 *     // e.g. 'editor' => ['admin'] (admin is parent of editor)
 * ],
 * 'permissions' => [
 *     // for each role insert one or more permissions
 *     // e.g. 'admin' => ['admin.pages']
 * ]
 *
 */
return [
];
