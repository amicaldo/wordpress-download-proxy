Wordpress DownloadProxy Class
===============

Class DownloadProxy


* Class name: DownloadProxy
* Namespace: *set yourself if required* 

This DownloadProxy Class allows you to proxy your custom attachments though this class
and do further check if the current user is allowed to download this file.

Links like `/wp-upload/year/month/file.pdf` can replaced with a proxy link.
This class provides a codebase and can be modificated.

It does:
- provides `getPublicDownloadLink` 
- provides `canDownloadFile` which can be implemented or extended
- register /download/$attachmentid/$filename route
- handles GET request for that /download/ route
- display error message if `canDownloadFile` returns false

Example use 
-------
please take care of autoloading, require and class structure yourself.
DownloadProxy class should instanced in every page for example with `function.php`
to keep /download/ route working

functions.php
```
$downloadProxy = new DownloadProxy();
```

Template:

``` 
<a href="<?php echo $downloadProxy->getPublicDownloadLink($attachmentID); ?>
  <i class="fa fa-download"></i>
</a>
```


Public Methods
-------


### getPublicDownloadLink

    boolean|string DownloadProxy::getPublicDownloadLink($attachmentID)

returns proxy download url for an attachmentID
which can be used as public link



* Visibility: **public**


#### Arguments
* $attachmentID **INT** - ID of an Wordpress attachment


### canDownloadFile

    boolean DownloadProxy::canDownloadFile($attachmentID, $parameterFileName)

public function to check if current user can download an specific file.
Please customise this function for your individual requirements.
As example check we use a comparing between the real file name and the file which was expected.
With that check, it's not possible to download all files `/download/1`, `download/2` without knowing the filename


* Visibility: **public**


#### Arguments
* $attachmentID **INT**
* $parameterFileName **STRING** file name expected from filesystem


Internal Methods
-------

### downloadFile

    mixed DownloadProxy::downloadFile($wp_query)

Checks for set 'action' param set to 'downloadProxy'.

Loads attachment id and filename from params in uri
after that checks permission and reads file to browser to enable download.
Function called by wordpress filter `parse_query`. No need for manual call.

* Visibility: **public**


#### Arguments
* $wp_query **WP_Query Object** -  current query object



### getRawFile

    mixed DownloadProxy::getRawFile($attachmentID)

starts browser download of attachment called by `downloadFile` after permission check


* Visibility: **protected**


#### Arguments
* $attachmentID **INT**


