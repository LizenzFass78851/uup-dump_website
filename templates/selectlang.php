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
<h3 class="ui centered header">
    <div class="content">
        <i class="fitted cubes icon"></i>&nbsp;
        <?= htmlentities($updateTitle) ?>
    </div>
</h3>

<?php if($updateArch == 'arm64') styleCluelessUserArm64Warn(); ?>

<div class="ui two columns mobile stackable centered grid">
    <div class="column">
        <h3 class="ui header">
            <i class="globe icon"></i>
            <div class="content">
                <?= $s['chooseLang']; ?>
                <div class="sub header"><?= $s['chooseLangDesc'] ?></div>
            </div>
        </h3>

        <?php if($updateBlocked): ?>
            <div class="ui center aligned one column padded relaxed grid">
                <div class="row">
                    <div class="column">
                        <i class="huge <?= $noLangsIcon ?> icon"></i>
                    </div>
                </div>
                <div class="row">
                    <div class="column">
                        <p><?= $noLangsCause ?></p>
                        <?php if($generatePacksButton): ?>
                            <form action="selectlang.php" method="get">
                                <input type="hidden" name="id" value="<?= $updateId ?>" />
                                <input type="hidden" name="packs" value="1" />
                                <button class="ui fluid right labeled icon button">
                                    <i class="pencil alternate icon"></i>
                                    Generate metadata
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <form class="ui form" action="selectedition.php" method="get" id="langForm">
                <input type="hidden" name="id" value="<?= $updateId ?>">
                <div class="field">
                    <label><?= $s['lang'] ?></label>
                    <select class="ui search dropdown" name="pack">
                        <?php foreach($langs as $key => $val): ?>
                            <option value="<?= $key ?>" <?= ($key == $defaultLang) ? 'selected' : '' ?>>
                                <?= $val ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="ui fluid right labeled icon blue button" id="submitForm" type="submit">
                    <i class="right arrow icon"></i>
                    <?= $s['next'] ?>
                </button>
            </form>

            <div class="ui info message">
                <i class="info icon"></i>
                <?= $s['selectLangInfoText1'] ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="column">
        <h3 class="ui header">
            <i class="open folder icon"></i>
            <div class="content">
                <?= $s['browseFiles']; ?>
                <div class="sub header"><?= $s['browseFilesDesc']; ?></div>
            </div>
        </h3>

        <form class="ui form" action="findfiles.php" method="get">
            <div class="field">
                <label><?= $s['searchFiles']; ?></label>
                <div class="ui action input">
                    <input type="hidden" name="id" value="<?= htmlentities($updateId) ?>">
                    <input type="text" name="q" placeholder="<?= $s['searchForFiles'] ?>">
                    <button class="ui blue icon button" type="submit"><i class="search icon"></i></button>
                </div>
            </div>
        </form>

        <a class="ui fluid right labeled icon button" href="<?= $findFilesUrl; ?>" style="margin-top:1rem;">
            <i class="open folder icon"></i>
            <?= $s['allFiles']; ?>
        </a>

        <div class="ui positive message">
            <i class="paper plane icon"></i>
                <?php printf(
                    $s['toSearchForCUUseQuery'],
                    "<a href=\"$findFilesUrl&q=Windows KB\">Windows KB</a>"
                ); ?>
        </div>
    </div>
</div>

<div class="ui fluid tiny three steps">
      <div class="active step">
            <i class="world icon"></i>
            <div class="content">
                <div class="title"><?= $s['chooseLang'] ?></div>
                <div class="description"><?= $s['chooseLangDesc'] ?></div>
            </div>
      </div>

      <div class="step">
            <i class="archive icon"></i>
            <div class="content">
                <div class="title"><?= $s['chooseEdition'] ?></div>
                <div class="description"><?= $s['chooseEditionDesc'] ?></div>
            </div>
      </div>

      <div class="step">
            <i class="briefcase icon"></i>
            <div class="content">
                <div class="title"><?= $s['summary'] ?></div>
                <div class="description"><?= $s['summaryDesc'] ?></div>
            </div>
      </div>
</div>

<h4 class="ui horizontal divider">
    <?= $s['information']; ?>
</h4>

<div class="ui three columns mobile stackable centered grid" style="margin-top: 1em;">
    <div class="column">
        <h4 class="ui center aligned tiny icon header">
            <i class="archive icon"></i>
            <div class="content">
                <?= $s['build']; ?>
                <div class="sub header"><?= $build ?></div>
            </div>
        </h4>
    </div>

    <div class="column">
        <h4 class="ui center aligned tiny icon header">
            <i class="cogs icon"></i>
            <div class="content">
                <?= $s['ring']; ?>
                <div class="sub header"><?= $fancyChannelName ?></div>
            </div>
        </h4>
    </div>

    <div class="column">
        <h4 class="ui center aligned tiny icon header">
            <i class="calendar icon"></i>
            <div class="content">
                <?= $s['dateAdded']; ?>
                <div class="sub header">
                    <?php if($created == null): ?>
                        <?= $s['unknown'] ?>
                    <?php else: ?>
                        <?= date("Y-m-d H:i:s T", $created) ?>
                    <?php endif; ?>
                </div>
            </div>
        </h4>
    </div>
</div>

<script>
    $('select.dropdown').dropdown();
    $('.ui.radio.checkbox').checkbox();
</script>
