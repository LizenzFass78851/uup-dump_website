<?php 
/*
Copyright 2022 UUP dump authors

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
if(!isset($templateOk)) die();
?>
<div class="welcome-text">
    <p class="header"><?= $s['uupdump'] ?></p>
    <p class="sub"><i><?= $s['slogan'] ?></i></p>
</div>

<form class="ui form" action="known.php" method="get">
    <div class="field">
        <div class="ui big action input">
            <input type="text" name="q" placeholder="<?= $s['seachForBuilds'] ?>">
            <button class="ui big blue icon button" type="submit"><i class="search icon"></i></button>
        </div>
    </div>
</form>

<div class="quick-search-buttons">
    <div class="ui tiny compact menu">
        <a class="item" href="known.php?q=regex:Insider.*(2((2(?!000|6[2-4][1-9])\d{3})|(5(?!398)\d{3})|[6-9]\d{3}))\.[1-9]|([3-9]\d{4})\.[1-9]">
            <i class="search icon"></i>
            <?= $s['channel_canary'] ?>
        </a>
    </div>

    <div class="ui tiny compact menu">
        <a class="item" href="known.php?q=regex:Insider.*(2[3-4]\d{3}|260[5-9]\d|261\d{2})\.[1-9]">
            <i class="search icon"></i>
            <?= $s['channel_dev'] ?>
        </a>
    </div>

    <div class="ui tiny compact menu">
        <div class="ui dropdown item">
            <i class="search icon"></i>
            Windows 11
            <i class="dropdown icon"></i>

            <div class="menu">
                <a class="item" href="known.php?q=regex:Insider.*26120">
                    24H2 Dev
                </a>
                <a class="item" href="known.php?q=regex:^(?:(?!Insider|Server|HCI).)*26100">
                    24H2
                </a>
                <a class="item" href="known.php?q=regex:Insider.*2263\d">
                    23H2 Beta
                </a>
                <a class="item" href="known.php?q=regex:^(?:(?!Insider).)*22631">
                    23H2
                </a>
                <a class="item" href="known.php?q=regex:^(?:(?!Insider).)*22621">
                    22H2
                </a>
                <a class="item" href="known.php?q=regex:^(?:(?!Insider).)*22000">
                    21H2
                </a>
            </div>
        </div>
    </div>

    <div class="ui tiny compact menu">
        <div class="ui dropdown item">
            <i class="search icon"></i>
            Windows Server
            <i class="dropdown icon"></i>

            <div class="menu">
                <a class="item" href="known.php?q=regex:(Server|HCI).*26100">
                    24H2
                </a>
                <a class="item" href="known.php?q=25398">
                    23H2
                </a>
                <a class="item" href="known.php?q=20349">
                    22H2
                </a>
                <a class="item" href="known.php?q=20348">
                    21H2
                </a>
            </div>
        </div>
    </div>

    <div class="ui tiny compact menu">
        <div class="ui dropdown item">
            <i class="search icon"></i>
            Windows 10
            <i class="dropdown icon"></i>

            <div class="menu">
                <a class="item" href="known.php?q=19045">
                    22H2
                </a>
                <a class="item" href="known.php?q=19044">
                    21H2
                </a>
                <a class="item" href="known.php?q=regex:^(?:(?!Insider).)*17763">
                    1809
                </a>
            </div>
        </div>
    </div>
</div>

<script>$('.ui.dropdown').dropdown();</script>

<h3 class="ui centered header">
    <div class="content">
        <i class="fitted rocket icon"></i>&nbsp;
        <?= $s['quickOptions'] ?>
    </div>
</h3>

<table class="ui large tablet stackable padded table">
    <thead>
        <tr>
            <th><?= $s['tHeadReleaseType'] ?></th>
            <th><?= $s['tHeadDescription'] ?></th>
            <th><?= $s['tHeadArchitectures'] ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="collapsing">
                <i class="large box icon"></i>
                <b><?= $s['latestPublicRelease'] ?></b>
            </td>
            <td><?= $s['latestPublicReleaseSub'] ?></td>
            <td class="center aligned collapsing">
                <a href="fetchupd.php?arch=amd64&ring=retail&build=<?= $retailLatestBuild ?>"><button class="ui blue button">x64</button></a>
                <a href="fetchupd.php?arch=arm64&ring=retail&build=<?= $retailLatestBuild ?>"><button class="ui button">arm64</button></a>
                <a href="fetchupd.php?arch=all&ring=retail&build=<?= $retailLatestBuild ?>"><button class="ui black button">all</button></a>
            </td>
        </tr>
        <tr>
            <td class="collapsing">
                <i class="large fire extinguisher icon"></i>
                <b><?= $s['latestRPRelease'] ?></b>
            </td>
            <td><?= $s['latestRPReleaseSub'] ?></td>
            <td class="center aligned">
                <a href="fetchupd.php?arch=amd64&ring=rp&build=<?= $rpLatestBuild ?>"><button class="ui blue button">x64</button></a>
                <a href="fetchupd.php?arch=arm64&ring=rp&build=<?= $rpLatestBuild ?>"><button class="ui button">arm64</button></a>
				<a href="fetchupd.php?arch=all&ring=rp&build=<?= $rpLatestBuild ?>"><button class="ui black button">all</button></a>
            </td>
        </tr>
        <tr>
            <td class="collapsing">
                <i class="large fire icon"></i>
                <b><?= $s['latestBetaRelease'] ?></b>
            </td>
            <td><?= $s['latestBetaReleaseSub'] ?></td>
            <td class="center aligned">
                <a href="fetchupd.php?arch=amd64&ring=wis&build=<?= $betaLatestBuild ?>"><button class="ui blue button">x64</button></a>
                <a href="fetchupd.php?arch=arm64&ring=wis&build=<?= $betaLatestBuild ?>"><button class="ui button">arm64</button></a>
				<a href="fetchupd.php?arch=all&ring=wis&build=<?= $betaLatestBuild ?>"><button class="ui black button">all</button></a>
            </td>
        </tr>
        <tr>
            <td class="collapsing">
                <i class="large bomb icon"></i>
                <b><?= $s['latestDevRelease'] ?></b>
            </td>
            <td><?= $s['latestDevReleaseSub'] ?></td>
            <td class="center aligned">
                <a href="fetchupd.php?arch=amd64&ring=wif&build=<?= $devLatestBuild ?>"><button class="ui blue button">x64</button></a>
                <a href="fetchupd.php?arch=arm64&ring=wif&build=<?= $devLatestBuild ?>"><button class="ui button">arm64</button></a>
				<a href="fetchupd.php?arch=all&ring=wif&build=<?= $devLatestBuild ?>"><button class="ui black button">all</button></a>
            </td>
        </tr>
        <tr>
            <td class="collapsing">
                <i class="large flask icon"></i>
                <b><?= $s['latestCanaryRelease'] ?></b>
            </td>
            <td><?= $s['latestCanaryReleaseSub'] ?></td>
            <td class="center aligned">
                <a href="fetchupd.php?arch=amd64&ring=canary&build=latest"><button class="ui blue button">x64</button></a>
                <a href="fetchupd.php?arch=arm64&ring=canary&build=latest"><button class="ui button">arm64</button></a>
				<a href="fetchupd.php?arch=all&ring=canary&build=latest"><button class="ui black button">all</button></a>
            </td>
        </tr>
    </tbody>
</table>

<h3 class="ui centered header">
    <div class="content">
        <i class="fitted star outline icon"></i>&nbsp;
        <?= $s['newlyAdded'] ?>
    </div>
</h3>

<div id="recentBuildsTable">
    <div class="ui segment" style="height: 40em;">
        <p></p>
        <div class="ui active dimmer">
            <div class="ui loader"></div>
        </div>
    </div>
</div>

<script>
fetch('buildstable.php')
    .then((response) => response.text())
    .then((text) => {
        document.getElementById('recentBuildsTable').innerHTML = text
    })
    .catch((error) => {
        console.error(error);
    });
</script>
