<?php print ($this->header); ?>


<div class="container">
	<div class="row">
		<div class="span3 offset9">
			<div class="suggest"></div>
		</div>
	</div>
</div>
<div class="container blokic">
	<div class="row">
		<div class="span12 toprow">
			<h1 class="title"><?php print $this->predlog->title; ?></h1>
			<div class="temporarybox">
				<div class="suggestionup" data-id="<?php print $this->predlog->id; ?>"></div>
				<div class="votecount"><?php print $this->predlog->vote_plus; ?></div>
				<div class="suggestiondown" data-id="<?php print $this->predlog->id; ?>"></div>
				<div class="votecount"><?php print $this->predlog->vote_minus; ?></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<span class="author"><?php print $this->user->name; ?>, </span>
			<span class="suggestiontimestamp"><?php print $this->predlog->timestamp; ?></span>
			<span class="suggestionsocial">LAJK BATONS</span>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<p class="suggestiontext"><?php print $this->predlog->text; ?></p>
		</div>
	</div>
	<hr class="predlogcrta" />
	<div class="row">
		<div class="span6">
			<h1 class="documenttitle">Dokumenti</h1>
			<div class="documentbox">
			<?php foreach ($this->document as $key => $value) { ?>
				<a href="<?php print $value->path; ?>" class="documentlink"><?php print $value->title; ?> <span class="documenttype">(<?php print $value->type; ?>, <?php print $value->size; ?>)</span></a><br />
			<?php } ?>	
				<div class="adddocument"></div>
				<form class="adddocumentbox">
					<input type="text" name="documentname" id="documentname" placeholder="ime dokumenta" />
					<label for="documentfile" id="uploadlabel"></label>
					<input type="file" name="documentfile" id="documentfile" />
					<input type="submit" id="submitdocument" value="" />
				</form>
			</div>
		</div>
		<div class="span6">
			<h1 class="workgrouptitle">Delovna skupina</h1>
			<div class="workgroupbox">
				<p class="workgrouptext">Iniciativa mi veliko pomeni ...</p>
				<div class="toggleworkgroup"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span6">
			<h1 class="documenttitle">Prednosti</h1>
			<?php
			foreach ($this->argplus as $key => $value) { ?>
				<div class="row">

					<div class="span6">
						<div class="argumentboxfor">
							<p class="authorship"><?php print $value->name; ?>, <?php print $value->timestamp; ?></p>
							<p class="argumenttext"><?php print $value->text; ?></p>
							<div class="argumentup" data-id="<?php print $value->id_argument; ?>"></div>
							<div class="argumentdown" data-id="<?php print $value->id_argument; ?>"></div>
							<hr class="argumentcrta" />
						</div>
					</div>
						

				</div>
			<?php }?>

			<div class="row">
				<div class="span6">
					<div class="addargumentfor">
						<img src="/static/img/human.png"><!-- INJECT SRC FROM FACEBOOK -->
						<textarea id="argumentinput"></textarea>
						<div class="socialconnect">
							<p class="connectwith">Poveži se z</p>
							<img src="/static/img/gumb_fb.png" class="fbsignin"/>
							<img src="/static/img/gumb_twitter.png" class="twsignin"/>
							<img src="/static/img/gumb_g+.png" class="googlesign"/>
						</div>
						<div class="createaccount">
							<p>ali si ustvari račun</p>
							<input type="text" class="accountname" placeholder="ime in priimek" />
							<input type="email" class="accountemail" placeholder="email" />
							<div class="submitargumentfor"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="span6">
			<h1 class="documenttitle">Zadržki</h1>
			<?php
			foreach ($this->argminus as $key => $value) { ?>
				<div class="row">

					<div class="span6">
						<div class="argumentboxagainst">
							<p class="authorship"><?php print $value->name; ?>, <?php print $value->timestamp; ?></p>
							<p class="argumenttext"><?php print $value->text; ?></p>
							<div class="argumentup" data-id="<?php print $value->id_argument; ?>"></div>
							<div class="argumentdown" data-id="<?php print $value->id_argument; ?>"></div>
							<hr class="argumentcrta" />
						</div>
					</div>
						

				</div>
			<?php }?>
			

			<div class="addargumentagainst">
				<img src="/static/img/human.png"><!-- INJECT SRC FROM FACEBOOK -->
				<textarea id="argumentinput"></textarea>
				<div class="socialconnect">
					<p class="connectwith">Poveži se z</p>
					<img src="/static/img/gumb_fb.png" class="fbsignin"/>
					<img src="/static/img/gumb_twitter.png" class="twsignin"/>
					<img src="/static/img/gumb_g+.png" class="googlesign"/>
				</div>
				<div class="createaccount">
					<p>ali si ustvari račun</p>
					<input type="text" class="accountname" placeholder="ime in priimek"/>
					<input type="email" class="accountemail" placeholder="email"/>
					<div class="submitargumentagainst"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" value="<?php print $this->id_right; ?>" id="rightid"/>
<div class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Modal header</h3>
	</div>
	<div class="modal-body">
		<p>One fine body…</p>
		<form>
			<input type="text" class="addsuggestiontitle" />
			<textarea class="addsuggestioncontent"></textarea>
		</form>
	</div>
</div>



<?php print ($this->footer); ?>