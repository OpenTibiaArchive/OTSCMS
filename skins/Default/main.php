<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2007 Wrzasq (wrzasq@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $config['site.title']; ?></title>
<?php foreach($this->javaScripts as $javaScript): ?>
        <script type="text/javascript" src="<?php echo $this->skin; ?>javascript/<?php echo $javaScript; ?>.js"></script>
<?php endforeach; ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $this->skin; ?>style.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->skin; ?>default.css" />
        <script type="text/javascript">

BaseHref = "<?php echo $this->skin; ?>";

Language = new Array();
Language[0] = "<?php echo $language['main.admin.ConfirmDelete']; ?>";
Language[1] = "<?php echo $language['main.admin.EditSubmit']; ?>";
Language[2] = "<?php echo $language['main.admin.DeleteSubmit']; ?>";
Language[3] = "<?php echo $language['main.admin.UpdateSubmit']; ?>";
Language[4] = "<?php echo $language['Modules.Statistics.HighscoresRanks']; ?>";
Language[5] = "<?php echo $language['Modules.Poll.OptionUpdated']; ?>";
Language[6] = "<?php echo $language['main.admin.InsertSubmit']; ?>";
Language[7] = "<?php echo $language['Modules.Forum.NoPosts']; ?>";
Language[8] = "<?php echo $language['Components.BBEditor.EnterText']; ?>";
Language[9] = "<?php echo $language['Components.BBEditor.link']; ?>";
Language[10] = "<?php echo $language['Components.BBEditor.createlink']; ?>";
Language[11] = "<?php echo $language['Components.BBEditor.email']; ?>";
Language[12] = "<?php echo $language['Components.BBEditor.list']; ?>";
Language[13] = "<?php echo $language['Components.BBEditor.item']; ?>";
Language[14] = "<?php echo $language['Modules.Account.PleaseEMail']; ?>";

        </script>
    </head>
    <body>
        <div id="menu_Home" class="module_menu">
            <div><a href="account.php"><?php echo $language['main.menu.Account.Signup']; ?></a></div>
            <div><a href="download.php"><?php echo $language['main.menu.Download.List']; ?></a></div>
            <div><a href="gallery.php"><?php echo $language['main.menu.Gallery.List']; ?></a></div>
            <div><a href="index.php"><?php echo $language['main.menu.News.Home']; ?></a></div>
            <div><a href="index.php?command=list"><?php echo $language['main.menu.News.List']; ?></a></div>
            <div><a href="index.php?command=archive"><?php echo $language['main.menu.News.Archive']; ?></a></div>
        </div>
        <div id="menu_Community" class="module_menu">
            <div><a href="character.php"><?php echo $language['main.menu.Character.Display']; ?></a></div>
            <div><a href="guild.php"><?php echo $language['main.menu.Guild.List']; ?></a></div>
            <div><a href="poll.php"><?php echo $language['main.menu.Poll.Latest']; ?></a></div>
            <div><a href="poll.php?command=list"><?php echo $language['main.menu.Poll.List']; ?></a></div>
        </div>
        <div id="menu_Forum" class="module_menu">
            <div><a href="forum.php"><?php echo $language['main.forum']; ?></a></div>
            <div><a href="priv.php"><?php echo $language['main.menu.PM.Inbox']; ?></a></div>
            <div><a href="priv.php?command=sent"><?php echo $language['main.menu.PM.Sent']; ?></a></div>
            <div><a href="priv.php?command=new"><?php echo $language['main.menu.PM.New']; ?></a></div>
        </div>
        <div id="menu_Library" class="module_menu">
            <div><a href="library.php?command=monsters"><?php echo $language['main.menu.Library.Monsters']; ?></a></div>
            <div><a href="library.php?command=spells"><?php echo $language['main.menu.Library.Spells']; ?></a></div>
        </div>
        <div id="menu_Statistics" class="module_menu">
            <div><a href="statistics.php?command=highscores"><?php echo $language['main.menu.Statistics.HighscoresExperience']; ?></a></div>
            <div><a href="statistics.php?command=highscores&amp;list=maglevel"><?php echo $language['main.menu.Statistics.HighscoresMagic']; ?></a></div>
            <div><a href="statistics.php?command=skills&amp;list=shielding"><?php echo $language['main.menu.Statistics.HighscoresShielding']; ?></a></div>
            <div><a href="statistics.php?command=skills&amp;list=distance"><?php echo $language['main.menu.Statistics.HighscoresDistance']; ?></a></div>
            <div><a href="statistics.php?command=skills&amp;list=sword"><?php echo $language['main.menu.Statistics.HighscoresSword']; ?></a></div>
            <div><a href="statistics.php?command=skills&amp;list=club"><?php echo $language['main.menu.Statistics.HighscoresClub']; ?></a></div>
            <div><a href="statistics.php?command=skills&amp;list=axe"><?php echo $language['main.menu.Statistics.HighscoresAxe']; ?></a></div>
            <div><a href="statistics.php?command=skills&amp;list=fist"><?php echo $language['main.menu.Statistics.HighscoresFist']; ?></a></div>
            <div><a href="statistics.php?command=skills&amp;list=fishing"><?php echo $language['main.menu.Statistics.HighscoresFishing']; ?></a></div>
            <div><a href="statistics.php"><?php echo $language['main.menu.Statistics.Census']; ?></a></div>
        </div>
        <div id="ajaxWrapper">
            <div id="ajaxMain">
                [<a id="ajaxClose" href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="document.getElementById('ajaxWrapper').hide(); return false;">X</a>]
            </div>
        </div>
        <div id="pageWrapper">
            <div id="pageHeader">
                <div id="pageTop">
<?php foreach($this['languages'] as $name): ?>
                    &nbsp;<a href="?language=<?php echo $name; ?>"><img alt="<?php echo $name; ?>" src="<?php echo $this->skin; ?>images/<?php echo $name; ?>.gif" /></a>&nbsp;
<?php endforeach; ?>
                    <br />
                    <form action="" method="post"><div>
                        <select onchange="this.form.submit();" name="template">
<?php foreach($this['skins'] as $name): ?>
                            <option value="<?php echo $name; ?>"
    <?php if($name == $config['site.skin']): ?>
                             selected="selected"
    <?php endif; ?>
                            ><?php echo $name; ?></option>
<?php endforeach; ?>
                        </select>
                    </div></form>
                </div>
                <h1>OTSCMS</h1>
                <h2>Open Tibia Server Content Management System</h2>
            </div>
            <div id="pageNav">
                <div onmouseover="changeMenu('menu_Home');" id="menu_Home_Btn"><a href="index.php" onclick="toogleMenu('menu_Home'); return false;"><?php echo $language['main.home']; ?></a></div>
                <div onmouseover="changeMenu('menu_Community');" id="menu_Community_Btn"><a href="character.php" onclick="toogleMenu('menu_Community'); return false;"><?php echo $language['main.community']; ?></a></div>
                <div onmouseover="changeMenu('menu_Forum');" id="menu_Forum_Btn"><a href="forum.php" onclick="toogleMenu('menu_Forum'); return false;"><?php echo $language['main.forum']; ?></a></div>
                <div onmouseover="changeMenu('menu_Library');" id="menu_Library_Btn"><a href="library.php" onclick="toogleMenu('menu_Library'); return false;"><?php echo $language['main.library']; ?></a></div>
                <div onmouseover="changeMenu('menu_Statistics');" id="menu_Statistics_Btn"><a href="statistics.php" onclick="toogleMenu('menu_Statistics'); return false;"><?php echo $language['main.statistics']; ?></a></div>
            </div>
<?php if( User::hasAccess(3) ): ?>
            <div id="pageAdmin">
                [<a href="admin.php?module=Character&amp;command=manage"><?php echo $language['main.menu.admin.Character_Manage']; ?></a>]
                [<a href="admin.php?module=Account&amp;command=manage"><?php echo $language['main.menu.admin.Account_Manage']; ?></a>]
                [<a href="admin.php?module=Character&amp;command=select"><?php echo $language['main.menu.admin.Settings_Character']; ?></a>]
                [<a href="admin.php?module=Settings&amp;command=manage"><?php echo $language['main.menu.admin.Settings_CMS']; ?></a>]
                [<a href="admin.php?module=News&amp;command=manage"><?php echo $language['main.menu.admin.News']; ?></a>]
                [<a href="admin.php?module=IPBan&amp;command=manage"><?php echo $language['main.menu.admin.IPBan']; ?></a>]
                [<a href="admin.php?module=Access&amp;command=manage"><?php echo $language['main.menu.admin.Access']; ?></a>]
                [<a href="admin.php?module=PM&amp;command=manage"><?php echo $language['main.menu.admin.PM']; ?></a>]
                [<a href="admin.php?module=MSP&amp;command=select"><?php echo $language['main.menu.admin.MSP']; ?></a>]
                [<a href="admin.php?module=Logger&amp;command=manage"><?php echo $language['main.menu.admin.Logger']; ?></a>]<br />
            </div>
<?php endif; ?>
            <div id="pageMenu">

<div class="menuContent">
<?php if(User::$logged): ?>
    <?php if($this['unread'] > 0): ?>
    &nbsp;&nbsp;.: <a href="priv.php" style="font-weight: bold;"><?php echo $language['Skins.Default.Inbox']; ?></a> :.<br />
    <?php endif; ?>
    &nbsp;&nbsp;.: <a href="account.php"><?php echo $language['main.menu.Account.Manage']; ?></a> :.<br />
    &nbsp;&nbsp;.: <a href="character.php?command=create"><?php echo $language['main.menu.Account.Character']; ?></a> :.<br />
    &nbsp;&nbsp;.: <a href="account.php?userlogout=1"><?php echo $language['Skins.Default.Logout']; ?></a> :.<br />
    &nbsp;&nbsp;.: <a href="account.php?command=suspend" style="color: #FF0000;" onclick="return confirm('<?php echo $language['Skins.Default.SuspendConfirm']; ?>');"><?php echo $language['main.menu.Account.Suspend']; ?></a> :.
<?php else: ?>
    <form action="account.php" method="post">
        <div>
            <?php echo $language['Skins.Default.account']; ?>:<br />
            <input type="password" name="useraccount" /><br />
            <?php echo $language['Skins.Default.password']; ?>:<br />
            <input type="password" name="userpassword" /><br />
            <input type="submit" value="<?php echo $language['Skins.Default.logIn']; ?>" /><br />
            <label><input type="checkbox" name="userusecookielogin" value="1" /> <?php echo $language['Skins.Default.cookies']; ?>.</label>
        </div>
    </form>
    <?php echo $language['Skins.Default.register']; ?>
<?php endif; ?>
</div>

<div class="menuHeader"><?php echo $language['Skins.Default.OnlineHeader']; ?></div>
<div class="menuContent" id="pageOnlines">
<?php

if( User::hasAccess(3) )
{
    $form = new ComponentAdminForm($this);
    $form['action'] = 'admin.php?module=Online&amp;command=insert';
    $form['submit'] = $language['main.admin.InsertSubmit'];
    $form['id'] = 'onlineForm';

    $form->addField('online[name]', ComponentAdminForm::FieldText, $language['Modules.Online.Name']);
    $form->addField('online[content]', ComponentAdminForm::FieldText, $language['Modules.Online.Content']);
    $form->addField('online[port]', ComponentAdminForm::FieldText, $language['Modules.Online.Port']);
    echo $form->display();
}

?>
<?php foreach($this['onlines'] as $id => $row): ?>
<div id="onlineID_<?php echo $id; ?>">
    <div class="floatRight"><?php echo $row['content']; ?></div>
    <?php echo $row['name']; ?>
    <?php if( User::hasAccess(3) ): ?>
<br /><a href="admin.php?module=Online&amp;command=edit&amp;id=<?php echo $id; ?>" onclick="return pageOnline.edit(<?php echo $id; ?>);"><img src="<?php echo $this->skin; ?>images/edit.gif" alt="<?php echo $language['main.admin.EditSubmit']; ?>" /></a>
<a href="admin.php?module=Online&amp;command=remove&amp;id=<?php echo $id; ?>" onclick="if( confirm(Language[0]) ) { return pageOnline.remove(<?php echo $id; ?>); } else { return false; }"><img src="<?php echo $this->skin; ?>images/delete.gif" alt="<?php echo $language['main.admin.DeleteSubmit']; ?>" /></a>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

            </div>
            <div id="pageMain">
<?php $this->run(); ?>
            </div>
            <div id="pageLinks">
<?php

if( User::hasAccess(3) )
{
    $form = new ComponentAdminForm($this);
    $form['action'] = 'admin.php?module=Links&amp;command=insert';
    $form['submit'] = $language['main.admin.InsertSubmit'];
    $form['id'] = 'linkForm';

    $form->addField('link[name]', ComponentAdminForm::FieldText, $language['Modules.Links.Name']);
    $form->addField('link[content]', ComponentAdminForm::FieldText, $language['Modules.Links.Content']);
    echo $form->display();
}

?>
<?php foreach($this['links'] as $id => $row): ?>
    <?php if( User::hasAccess(3) ): ?>
<div id="linkID_<?php echo $id; ?>" class="adminFrontend">
    <?php endif; ?>
    <a class="outLink" href="<?php echo $row['content']; ?>"><?php echo $row['name']; ?></a>
    <?php if( User::hasAccess(3) ): ?>
<a href="admin.php?module=Links&amp;command=edit&amp;id=<?php echo $id; ?>" onclick="return pageLinks.edit(<?php echo $id; ?>);"><img src="<?php echo $this->skin; ?>images/edit.gif" alt="<?php echo $language['main.admin.EditSubmit']; ?>" /></a>
<a href="admin.php?module=Links&amp;command=remove&amp;id=<?php echo $id; ?>" onclick="if( confirm(Language[0]) ) { return pageLinks.remove(<?php echo $id; ?>); } else { return false; }"><img src="<?php echo $this->skin; ?>images/delete.gif" alt="<?php echo $language['main.admin.DeleteSubmit']; ?>" /></a></div>
    <?php endif; ?>
<?php endforeach; ?>
            </div>
            <div id="pageFooter">
                Powered by <a href="http://www.otscms.com/" class="outLink">OTSCMS</a> v <?php echo $config['version']; ?>; Copyright &copy; 2005 - 2007 by <a href="http://www.wrzasq.com/" class="outLink">Wrzasq</a>.
            </div>
        </div>
    </body>
</html>
