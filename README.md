OpenVPNWebGUI
https://github.com/korylprince/OpenVPNWebGUI

#Installing#

This was installed on a server running Ubuntu Server.
This should work on any recent version of Ubuntu/Debian. You should be able to make this work on any machine with a webserver, PHP, and OpenVPN, but you may have to change paths in auth/zipgen.php and auth/download.php

If you are using a different authentication method, you may need something more. Check the KAuth requirements here:
https://github.com/korylprince/KAuth

Simply copy the OpenVPNWebGUI folder to your web directory and rename to "vpn". Then edit copy auth/options.php.def to auth/options.php and edit it for authentication options.

Make sure to restrict access to the files folder. Do this either in the server configuration or in an .htaccess file. Otherwise users can just download certificates from your server.
Also make sure your restrict access to auth/users.list

Place ca.crt and ta.key in files/keys.

Make sure that your webserver user has read/write access to /etc/openvpn/keys.

Then navigate to your website and login.

If you have any issues or questions, email the email address below, or open an issue at:
https://github.com/korylprince/OpenVPNWebGUI/issues

#Usage#

By default you can login using the default login is administrator with password "password".
This will log you into the admin interface where you can generate any certificate. All other usernames will be presented with an OS chooser interface.

It is recommended you set this up with a ldap server so that users can authenticate with their ldap logins.

I realize this README is lacking. It is simply presented as-is. I will be glad to help you set it up (see email below.)

This builds upon the "KAuth" Library:
https://github.com/korylprince/KAuth

The authentication can be extend using that library. Note: sessions must be used.

#Copyright Information#

jQuery and jQuery UI are produced by the jQuery team: http://jquery.com/ and http://jqueryui.com/

jQuery Color Plugin from http://www.bitstorm.org/jquery/color-animation/

session_lib.php was taken from the PHP manual: http://php.net/manual/en/function.session-set-save-handler.php

TunnelBlick from http://code.google.com/p/tunnelblick/

OpenVPN GUI from http://openvpn.se/

OS Logos Copyright their respective owners.

All other code is Copyright 2012 Kory Prince (korylprince at gmail dot com.) This code is licensed under the GPL v3 which is included in this distribution.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
