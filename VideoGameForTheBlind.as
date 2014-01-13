
package {
	import flash.display.*;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.KeyboardEvent;
	import flash.events.MouseEvent;
	import flash.events.SampleDataEvent;
	import flash.events.TimerEvent;
	import flash.media.Sound;
	import flash.media.SoundChannel;
	import flash.media.SoundTransform;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.ui.Keyboard;
	import flash.ui.Mouse;
	import flash.utils.*;

	public class PlayGame extends Sprite {

		protected var footsteps_sound:Sound;
		protected var footsteps_channel:SoundChannel;
		protected var footsteps_volume_adjustment:Number = 0.40;
		protected var gun_sound:Sound;
		protected var gun_channel:SoundChannel;
		protected var gun_volume_adjustment:Number = 0.80;
		protected var reload_sound:Sound;
		protected var reload_channel:SoundChannel;
		protected var reload_volume_adjustment:Number = 0.80;
		protected var call_sound:Sound;
		protected var call_channel:SoundChannel;
		protected var call_volume_adjustment:Number = 0.20;
		protected var plink_sound:Sound;
		protected var plink_channel:SoundChannel;
		protected var eating_sound:Sound;
		protected var eating_channel:SoundChannel;
		protected var eating_volume_adjustment:Number = 0.40;
		protected var intro_music_sound:Sound;
		protected var intro_music_channel:SoundChannel;
		protected var intro_music_volume_adjustment:Number = 24.00;
		protected var title_sound:Sound;
		protected var title_channel:SoundChannel;
		protected var title_volume_adjustment:Number = 4.00;
		protected var background_music_sound:Sound;
		protected var background_music_channel:SoundChannel;
		protected var background_music_volume_adjustment:Number = 0.30;
		protected var ending_music_sound:Sound;
		protected var ending_music_channel:SoundChannel;
		protected var ending_music_volume_adjustment:Number = 30.00;
		protected var pre_instructions_sound:Sound;
		protected var pre_instructions_channel:SoundChannel;
		protected var pre_instructions_volume_adjustment:Number = 2.00;
		protected var instructions_sound:Sound;
		protected var instructions_channel:SoundChannel;
		protected var instructions_volume_adjustment:Number = 8.00;
		protected var restart_sound:Sound;
		protected var restart_channel:SoundChannel;
		protected var restart_volume_adjustment:Number = 4.00;
		protected var rescue_sound:Sound;
		protected var rescue_channel:SoundChannel;
		protected var rescue_volume_adjustment:Number = 9.00;

		protected var ticker:Timer;

		protected var PLAYER_LOCATION_Y:Number = 1;
		protected var ENEMY_LOCATION_Y:Number = 0;
		protected var MOUSE_BALANCE:Number = 0;
		protected var MOUSE_VOLUME:Number = 0;
		protected var ENEMY_BALANCE:Number = 0;
		protected var ENEMY_VOLUME:Number = 0;
		protected var DAMSEL_BALANCE:Number = 0;
		protected var DAMSEL_VOLUME:Number = 0;
		protected var MAX_VOLUME:Number = 1;
		protected var MIN_VOLUME:Number = 0.1;
		protected var ENEMY_HORIZONTAL_DIRECTION:String = "";
		protected var ENEMY_VERTICAL_DIRECTION:String = "";
		protected var ENEMY_TYPE:Number = 0;
		protected var ENEMY_FOOTSTEPS_TYPE:Number = 0;
		protected var ENEMY_TARGET_BALANCE:Number = 0;
		protected var NUM_ENEMY_TYPES:Number = 10;
		protected var NUM_ENEMY_FOOTSTEPS_TYPES:Number = 4;
		protected var NUM_FALLDOWN_TYPES:Number = 4;
		protected var NUM_LANDSCAPE_ITEMS:Number = 10;
		protected var NUM_MOVABLE_LANDSCAPE_ITEMS:Number = 9;
		protected var NUM_DAMSEL_TYPES:Number = 5;
		protected var NUM_FENCE_TYPES:Number = 3;
		protected var BULLETS:Number = 2;
		protected var LOADED_BULLETS:Number = 0;
		protected var MOUSE_X:Number = 0;
		protected var MOUSE_Y:Number = 0;
		protected var MOUSE_ON_STAGE:int = 0;
		protected var STAGE_WIDTH:Number = stage.stageWidth;
		protected var STAGE_HEIGHT:Number = stage.stageHeight;
		protected var READY_FOR_INPUT:Number = 0;
		protected var READY_FOR_MOUSE_ALERT:Number = 1;
		protected var READY_FOR_INSTRUCTIONS:Number = 1;
		protected var RANDOM:Number = 0;
		protected var INITIAL_GRACE_PERIOD:Number = 9;
		protected var INSTRUCTIONS_GRACE_PERIOD:Number = 0;
		protected var DIFFICULTY_LEVEL:uint = 1;
		protected var GAME_OVER:Number = 0;
		protected var SCORE:Number = 0;

		public function PlayGame():void {

			var i:uint = 0;

			// set initial location of damsel
			DAMSEL_VOLUME = MIN_VOLUME;
			DAMSEL_BALANCE = 2 - (Math.random() * 4);

			footsteps_sound = new Sound();
			footsteps_sound.load(new URLRequest("./footsteps_leaves.mp3"));

			gun_sound = new Sound();
			gun_sound.load(new URLRequest("./old_gunshot.mp3"));

			reload_sound = new Sound();
			reload_sound.load(new URLRequest("./reload.mp3"));

			call_sound = new Sound();
			call_sound.load(new URLRequest("./call_damsel.mp3"));

			rescue_sound = new Sound();
			rescue_sound.load(new URLRequest("./rescue.mp3"));

			pre_instructions_sound = new Sound();
			pre_instructions_sound.load(new URLRequest("./Game_Pre_Instructions.mp3"));

			instructions_sound = new Sound();
			instructions_sound.load(new URLRequest("./Game_Instructions.mp3"));

			restart_sound = new Sound();
			restart_sound.load(new URLRequest("./restart_message.mp3"));

			// define and load zombie sounds
			var zombie_filename:Array = new Array(
				"zombie_exhale.mp3",
				"zombie_grunt.mp3",
				"zombie_grunt2.mp3",
				"zombie_moan.mp3",
				"zombie_urgh.mp3",
				"zombie_snarl.mp3",
				"zombie_gurgle.mp3",
				"zombie_yawn.mp3",
				"zombie_huff.mp3",
				"zombie_breath.mp3",
				"zombie_bloogah.mp3"  
				);
			var zombie_volume_adjustment:Array = new Array(
				2.20,
				2.50,
				2.50,
				3.00,
				2.00,
				2.00,
				2.25,
				2.00,
				2.00,
				2.00,
				2.00
				);
			var zombie_sound:Array = new Array();
			for (i = 0; i <= (NUM_ENEMY_TYPES - 1); i++) {
				zombie_sound[i] = new Sound(new URLRequest("./" + zombie_filename[i]));
			}

			// define and load falldown sounds
			var falldown_filename:Array = new Array(
				"knockdown.mp3",
				"knockdown2.mp3",
				"knockdown3.mp3",
				"knockdown4.mp3"
				);
			var falldown_volume_adjustment:Array = new Array(
				0.95,
				1.00,
				0.90,
				1.00
				);
			var falldown_sound:Array = new Array();
			for (i = 0; i <= (NUM_FALLDOWN_TYPES - 1); i++) {
				falldown_sound[i] = new Sound(new URLRequest("./" + falldown_filename[i]));
			}

			// define and load landscape sounds
			var landscape_filename:Array = new Array(
				"frog.mp3",
				"cricket.mp3",
				"owl.mp3",
				"coyote.mp3",
				"cricket_high_pitch.mp3",
				"cricket_extended.mp3",
				"bigfrog.mp3",
				"cricket_whistling.mp3",
				"cricket.mp3",
				"breeze.mp3"
				);
			var landscape_balance:Array = new Array(
				-1,
				1,
				0,
				1.5,
				-1.25,
				-1.5,
				-1.75,
				1.25,
				1.75,
				0
				);
			var landscape_volume:Array = new Array(
				0.1,
				0.1,
				0.1,
				0.1,
				0.1,
				0.1,
				0.1,
				0.1,
				0.1,
				0.1
				);
			var landscape_occurrence_likelihood:Array = new Array(
				0.20,
				0.42,
				0.05,
				0.04,
				0.20,
				0.15,
				0.20,
				0.20,
				0.42,
				0.09
				);
			var landscape_sound:Array = new Array();
			for (i = 0; i <= NUM_LANDSCAPE_ITEMS - 1; i++) {
				landscape_sound[i] = new Sound(new URLRequest("./" + landscape_filename[i]));
			}

			// define and load enemy footsteps sounds
			var enemy_footsteps_filename:Array = new Array(
				"footstep_one_leaves.mp3",
				"footsteps_leaves_thud.mp3",
				"footsteps_leaves_thud_rustle.mp3",
				"footstep_thud.mp3"
				);
			var enemy_footsteps_volume_adjustment:Array = new Array(
				0.40,
				0.40,
				0.40,
				0.40
				);
			var enemy_footsteps_sound:Array = new Array();
			for (i = 0; i <= (NUM_ENEMY_FOOTSTEPS_TYPES - 1); i++) {
				enemy_footsteps_sound[i] = new Sound(new URLRequest("./" + enemy_footsteps_filename[i]));
			}

			// define and load damsel sounds
			var damsel_filename:Array = new Array(
				"child_cry_help.mp3",
				"child_cry_daddy.mp3",
				"child_cry_daddy_mama.mp3",
				"child_cry_short.mp3",
				"child_cry_shriek.mp3"
				);
			var damsel_volume_adjustment:Array = new Array(
				1.00,
				1.00,
				1.00,
				1.00,
				1.00
				);
			var damsel_sound:Array = new Array();
			for (i = 0; i <= (NUM_DAMSEL_TYPES - 1); i++) {
				damsel_sound[i] = new Sound(new URLRequest("./" + damsel_filename[i]));
			}

			// define and load fence sounds
			var fence_filename:Array = new Array(
				"gate_clang.mp3",
				"gate_double_clang.mp3",
				"gate_rattle.mp3"
				);
			var fence_volume_adjustment:Array = new Array(
				1.20,
				1.80,
				1.20
				);
			var fence_sound:Array = new Array();
			for (i = 0; i <= (NUM_FENCE_TYPES - 1); i++) {
				fence_sound[i] = new Sound(new URLRequest("./" + fence_filename[i]));
			}

			// define and load difficulty level message sounds
			var difficulty_volume_adjustment:Number = 3.00;
			var difficulty_sound:Array = new Array();
			for (i = 1; i <= 9; i++) {
				difficulty_sound[i] = new Sound(new URLRequest("./difficulty" + i + ".mp3"));
			}


			plink_sound = new Sound();
			plink_sound.load(new URLRequest("./peck.mp3"));

			intro_music_sound = new Sound();
			intro_music_sound.load(new URLRequest("./Dramatic_Intro.mp3"));
			intro_music_sound.addEventListener(Event.COMPLETE, soundLoadCompleteHandler1);

			title_sound = new Sound();
			title_sound.load(new URLRequest("./Game_Title.mp3"));

			ending_music_sound = new Sound();
			ending_music_sound.load(new URLRequest("./Dramatic_Horns_Riff.mp3"));

			eating_sound = new Sound();
			eating_sound.load(new URLRequest("./monster_eating_long.mp3"));

			background_music_sound = new Sound();
			background_music_sound.load(new URLRequest("./Strings_Intro.mp3"));
			background_music_sound.addEventListener(Event.COMPLETE, soundLoadCompleteHandler2);

			var swfStage:Stage = stage; 
			swfStage.scaleMode = StageScaleMode.EXACT_FIT;

			stage.addEventListener(MouseEvent.CLICK, reportClick);  // Listen for mouse click
			stage.addEventListener(MouseEvent.MOUSE_MOVE, CheckMouseOnStage);
			stage.addEventListener(KeyboardEvent.KEY_DOWN,reportKeyDown);

			ticker = new Timer(1000);
			ticker.addEventListener(TimerEvent.TIMER, onTick);
			ticker.start();

			graphics.beginFill(0x000000);
			graphics.drawRect(0, 0, STAGE_WIDTH, STAGE_HEIGHT);
			graphics.endFill();

			var text_message:TextField = new TextField();
			text_message.textColor = 0xFFFFFF;
			text_message.width = 400;
			text_message.autoSize = TextFieldAutoSize.CENTER;
			addChild(text_message);
			ShowTextMessage("Audio only");

			var text_message2:TextField = new TextField();
			text_message2.textColor = 0xFFFFFF;
			text_message2.width = 400;
			text_message2.autoSize = TextFieldAutoSize.CENTER;
			addChild(text_message2);
			ShowTextMessage2("Click now for instructions");

			// When timer reaches every 1 second
			function onTick(event:TimerEvent):void { 
				if (READY_FOR_INPUT != -1) { READY_FOR_INPUT = 1; }
				if (INITIAL_GRACE_PERIOD == 0 && INSTRUCTIONS_GRACE_PERIOD == 0) {
					SoundLandscape();
					if (DAMSEL_VOLUME != -1) { SoundDamsel(); }
				}
				if (INITIAL_GRACE_PERIOD == 0 && ENEMY_VOLUME == 0 && INSTRUCTIONS_GRACE_PERIOD == 0) { CheckToSpawnEnemy(); }
				if (INITIAL_GRACE_PERIOD >= 1) {
					INITIAL_GRACE_PERIOD = INITIAL_GRACE_PERIOD - 1;
					if (INITIAL_GRACE_PERIOD == 0) {
						//PlayPreInstructions();
						ShowTextMessage("");
						ShowTextMessage2("");
					}
				}
				if (INSTRUCTIONS_GRACE_PERIOD >= 1) {
					INSTRUCTIONS_GRACE_PERIOD = INSTRUCTIONS_GRACE_PERIOD - 1;
					if (INSTRUCTIONS_GRACE_PERIOD == 0) {  // start background music back up
						var background_music_trans:SoundTransform = new SoundTransform(MIN_VOLUME * background_music_volume_adjustment, 0); 
						background_music_channel = background_music_sound.play(0, 999, background_music_trans);
					}
				}
				if (ENEMY_VOLUME > 0) {
					SoundEnemy();
				}
			}

			function ShowTextMessage(xtext:String):void {
				text_message.text = xtext;

				text_message.x = Math.round((STAGE_WIDTH - text_message.textWidth) / 2);
				text_message.y = Math.round((STAGE_HEIGHT - text_message.textWidth) / 2);
			}

			function ShowTextMessage2(xtext:String):void {
				text_message2.text = xtext;

				text_message2.x = Math.round((STAGE_WIDTH - text_message2.textWidth) / 2);
				text_message2.y = Math.round((STAGE_HEIGHT - text_message2.textWidth) / 2);
			}

			// When mouse is clicked 
			function reportClick(event:MouseEvent):void {
				if (GAME_OVER == 1) { RestartGame(); }
				else if (INITIAL_GRACE_PERIOD > 0) { PlayInstructions(); }
				else { ShootGun(); }
			}

			function DamselTakeStep():void {
				var horizontal_step_amount:Number = 0.10;
				var vertical_step_amount:Number = 0;
				RANDOM = Math.random();
				if (RANDOM < 0.25) {
					// move in a random direction
					RANDOM = Math.random();
					if (RANDOM <= 0.25) { DAMSEL_BALANCE = DAMSEL_BALANCE + horizontal_step_amount; }
					else if (RANDOM <= 0.50) { DAMSEL_BALANCE = DAMSEL_BALANCE - horizontal_step_amount; }
					else if (RANDOM <= 0.75) { DAMSEL_VOLUME = DAMSEL_VOLUME - vertical_step_amount; }
					else { DAMSEL_VOLUME = DAMSEL_VOLUME + vertical_step_amount; }

					if (DAMSEL_BALANCE > 2.0) { DAMSEL_BALANCE = -2.0; } // if scrolled so far to the right, start over on left
					else if (DAMSEL_BALANCE < -2.0) { DAMSEL_BALANCE = 2.0; } // if scrolled so far to the left, start over on right
				}
			}

			function EnemyTakeStep():void {
				var horizontal_step_amount:Number = 0.15;
				var vertical_step_amount:Number = 0.05;
				if ( (DIFFICULTY_LEVEL >= 4) && (DIFFICULTY_LEVEL <= 6) ) { vertical_step_amount = 0.075; }
				else if (DIFFICULTY_LEVEL >= 7) { vertical_step_amount = 0.10; }

				// home in on center (or someplace near the center)
				if (ENEMY_BALANCE < ENEMY_TARGET_BALANCE) { ENEMY_HORIZONTAL_DIRECTION = "right"; }
				else if (ENEMY_BALANCE > ENEMY_TARGET_BALANCE) { ENEMY_HORIZONTAL_DIRECTION = "left"; }
				else { ENEMY_HORIZONTAL_DIRECTION = ""; }

				// but occasionally move to the opposite direction to be unpredictable
				RANDOM = Math.random();
				if (RANDOM < 0.20) {
					if (ENEMY_HORIZONTAL_DIRECTION == "right") { ENEMY_HORIZONTAL_DIRECTION = "left"; }
					else if (ENEMY_HORIZONTAL_DIRECTION == "left") { ENEMY_HORIZONTAL_DIRECTION = "right"; }
				}

				if (ENEMY_HORIZONTAL_DIRECTION == "right") { ENEMY_BALANCE = ENEMY_BALANCE + horizontal_step_amount; }  // move to right
				if (ENEMY_HORIZONTAL_DIRECTION == "left") { ENEMY_BALANCE = ENEMY_BALANCE - horizontal_step_amount; }  // move to left
				if (ENEMY_VERTICAL_DIRECTION == "down") {  // move closer
					ENEMY_VOLUME = ENEMY_VOLUME + vertical_step_amount;
					ENEMY_LOCATION_Y = ENEMY_LOCATION_Y + vertical_step_amount;
				}

				// check if reached you
				var PLAYER_SIZE:Number = 0.30;
				if ( (DIFFICULTY_LEVEL >= 4) && (DIFFICULTY_LEVEL <= 6) ) { PLAYER_SIZE = 0.40; }
				else if (DIFFICULTY_LEVEL >= 7) { PLAYER_SIZE = 0.50; }
				if ( (ENEMY_VOLUME > MAX_VOLUME) &&
						(ENEMY_BALANCE > 0 - PLAYER_SIZE) &&
						(ENEMY_BALANCE < 0 + PLAYER_SIZE)
						) {
					EndGame();
				}
				// check if enemy has passed behind you (in which case remove from field)
				if (ENEMY_VOLUME > MAX_VOLUME) {
					if (ENEMY_LOCATION_Y > 0.95) {  // sound second gate  when zombie crosses it
							SoundFence(2, ENEMY_VOLUME, ENEMY_BALANCE); }
					ENEMY_VOLUME = 0;
					ENEMY_LOCATION_Y = 0;
				}

			}

			function EndGame():void {
				var ending_music_trans:SoundTransform = new SoundTransform(MIN_VOLUME * ending_music_volume_adjustment, 0); 
				var ending_music_channel:SoundChannel = ending_music_sound.play(0, 1, ending_music_trans);
				var eating_trans:SoundTransform = new SoundTransform(ENEMY_VOLUME * eating_volume_adjustment, 0); 
				var eating_channel:SoundChannel = eating_sound.play(0, 9, eating_trans);
				GAME_OVER = 1;
				READY_FOR_INPUT = -1;
				ENEMY_VOLUME = -1;
				BULLETS = -1;
				for (i = 0; i <= NUM_LANDSCAPE_ITEMS - 1; i++) {
					landscape_volume[i] = 0;
				}
				ShowTextMessage2("Score: " + SCORE);
				ShowTextMessage("Click to restart");
				var restart_trans:SoundTransform = new SoundTransform(MIN_VOLUME * restart_volume_adjustment, 0); 
				var restart_channel:SoundChannel = restart_sound.play(0, 1, restart_trans);
			}

			function RestartGame():void {
				var url:URLRequest = new URLRequest("http://www.soundalone.com");
				navigateToURL(url, "_self");
			}

			function CheckToSpawnEnemy():void {
				if (ENEMY_VOLUME == 0) {
					var spawn_likelihood:Number = 0.04;
					if ( (DIFFICULTY_LEVEL >= 2) && (DIFFICULTY_LEVEL <= 3) ) { spawn_likelihood = 0.05; }
					else if ( (DIFFICULTY_LEVEL >= 4) && (DIFFICULTY_LEVEL <= 5) ) { spawn_likelihood = 0.06; }
					else if ( (DIFFICULTY_LEVEL >= 6) && (DIFFICULTY_LEVEL <= 7) ) { spawn_likelihood = 0.07; }
					else if (DIFFICULTY_LEVEL >= 8) { spawn_likelihood = 0.08; }
					RANDOM = Math.random();
					if (RANDOM < spawn_likelihood) {
						ENEMY_VOLUME = MIN_VOLUME;
						ENEMY_BALANCE = 1 - (Math.random() * 2);
						ENEMY_LOCATION_Y = ENEMY_VOLUME - (1 - PLAYER_LOCATION_Y);
						ENEMY_VERTICAL_DIRECTION = "down";
						ENEMY_TARGET_BALANCE = 0.5 - Math.random();
						ENEMY_TYPE = Math.round(Math.random() * (NUM_ENEMY_TYPES - 1));
						ENEMY_FOOTSTEPS_TYPE = Math.round(Math.random() * (NUM_ENEMY_FOOTSTEPS_TYPES - 1));
						ShowTextMessage("");
						ShowTextMessage2("");
					}
				}

			}

			function FallDown():void {
				var FALLDOWN_TYPE:Number = Math.round(Math.random() * (NUM_FALLDOWN_TYPES - 1));
				var falldown_trans:SoundTransform = new SoundTransform(ENEMY_VOLUME * falldown_volume_adjustment[FALLDOWN_TYPE], ENEMY_BALANCE); 
				var falldown_channel:SoundChannel = falldown_sound[FALLDOWN_TYPE].play(0, 1, falldown_trans);

				ENEMY_VOLUME = 0;  // remove enemy from field
				SCORE = SCORE + 1;
			}

			function CallToDamsel():void {
				if ( (READY_FOR_INPUT == 1) && (DAMSEL_VOLUME != -1) ) {
					var call_trans:SoundTransform = new SoundTransform(MAX_VOLUME * call_volume_adjustment, 0); 
					var call_channel:SoundChannel = call_sound.play(0, 1, call_trans);
					READY_FOR_INPUT = 0;

					// rescued, end game
					if ( (DAMSEL_BALANCE > -0.1) && (DAMSEL_BALANCE < 0.1) && (DAMSEL_VOLUME > 0.90) ) {  // reached damsel
						var rescue_trans:SoundTransform = new SoundTransform(MIN_VOLUME * rescue_volume_adjustment, 0); 
						var rescue_channel:SoundChannel = rescue_sound.play(0, 1, rescue_trans);
						GAME_OVER = 1;
						DAMSEL_VOLUME = -1;  // deactivate damsel
						READY_FOR_INPUT = -1;
						ENEMY_VOLUME = -1;
						BULLETS = -1;
						for (i = 0; i <= NUM_LANDSCAPE_ITEMS - 1; i++) {
							landscape_volume[i] = MIN_VOLUME;
						}
						ShowTextMessage("Click to restart");
					}
				}
			}

			function ShootGun():void {
				if (READY_FOR_INPUT == 1 && MOUSE_ON_STAGE == 1) {
					MOUSE_BALANCE = Math.round( ( ((MOUSE_X / STAGE_WIDTH) * 2) - 1 ) * 10) / 10;
					MOUSE_VOLUME = MAX_VOLUME;
					if (BULLETS > 0) {
						BULLETS = BULLETS - 1;
						var gun_trans:SoundTransform = new SoundTransform(MOUSE_VOLUME * gun_volume_adjustment, MOUSE_BALANCE); 
						var gun_channel:SoundChannel = gun_sound.play(0, 1, gun_trans);
						var ENEMY_SIZE:Number = 0.20;
						if ( (DIFFICULTY_LEVEL >= 4) && (DIFFICULTY_LEVEL <= 6) ) { ENEMY_SIZE = 0.15; }
						else if (DIFFICULTY_LEVEL >= 7) { ENEMY_SIZE = 0.10; }
						if ( (ENEMY_VOLUME > 0) &&
								(ENEMY_BALANCE > MOUSE_BALANCE - ENEMY_SIZE) &&
								(ENEMY_BALANCE < MOUSE_BALANCE + ENEMY_SIZE)) {  // if hit enemy (this should actually be a %, not an absolute)
							FallDown();
						}
					}
					else if (BULLETS == 0) {
						var reload_trans:SoundTransform = new SoundTransform(MOUSE_VOLUME * reload_volume_adjustment, MOUSE_BALANCE); 
						var reload_channel:SoundChannel = reload_sound.play(0, 1, reload_trans);
						LOADED_BULLETS = LOADED_BULLETS + 1;
						if (LOADED_BULLETS == 2) {
							BULLETS = 2;
							LOADED_BULLETS = 0;
						}
					}
					READY_FOR_INPUT = 0;
				}
				else if (READY_FOR_INPUT == 1 && MOUSE_ON_STAGE == 0) {
					SoundPlink();
				}
			}

			function SoundPlink():void {
				var plink_trans:SoundTransform = new SoundTransform(MIN_VOLUME * 4, 0); 
				var plink_channel:SoundChannel = plink_sound.play(0, 1, plink_trans);
			}

			function SoundLandscape():void {
				var delay:Number = 0;
				var loops:Number = 1;

				for (i = 0; i < NUM_LANDSCAPE_ITEMS; i++) {
					if ( (landscape_balance[i] >= -1) && (landscape_balance[i] <= 1) ) {  // if on stage
						RANDOM = Math.random();
						if (RANDOM < landscape_occurrence_likelihood[i]) {
							delay = 0;
							loops = Math.round(Math.random()) + 1;
							if (i == 9) { landscape_balance[i] = 1 - (Math.random() * 2); }  // wind has random balance
							var landscape_trans:SoundTransform = new SoundTransform(landscape_volume[i], landscape_balance[i]); 
							var landscape_channel:SoundChannel = landscape_sound[i].play(delay, loops, landscape_trans);
						}
					}
				}
			}

			function SoundDamsel():void {
				var damsel_likelihood:Number = 0.01;
				RANDOM = Math.random();
				if (RANDOM < 0.50) {
					DamselTakeStep();
				}
				RANDOM = Math.random();
				if (RANDOM < damsel_likelihood) {
					if ( (DAMSEL_BALANCE >= -1) && (DAMSEL_BALANCE <= 1) ) {  // if on stage
						var DAMSEL_TYPE:Number = Math.round(Math.random() * (NUM_DAMSEL_TYPES - 1));
						var damsel_trans:SoundTransform = new SoundTransform(DAMSEL_VOLUME * damsel_volume_adjustment[DAMSEL_TYPE], DAMSEL_BALANCE); 
						var damsel_channel:SoundChannel = damsel_sound[DAMSEL_TYPE].play(0, 1, damsel_trans);
					}
				}
			}

			function SoundFence(FENCE_TYPE:Number, FENCE_VOLUME:Number, FENCE_BALANCE:Number):void {
				var fence_trans:SoundTransform = new SoundTransform(FENCE_VOLUME * fence_volume_adjustment[FENCE_TYPE], FENCE_BALANCE); 
				var fence_channel:SoundChannel = fence_sound[FENCE_TYPE].play(0, 1, fence_trans);
				READY_FOR_INPUT = 0;
			}

			function SoundEnemy():void {
				var delay:Number = 0;
				var loops:Number = 1;

				if (ENEMY_VOLUME > 0) {
					RANDOM = Math.random();
					if (RANDOM < 0.70) {
						if ( (ENEMY_LOCATION_Y > 0.05) && (ENEMY_LOCATION_Y < 0.15) ) {  // sound first fence when zombie crosses it
							SoundFence(1, ENEMY_VOLUME, ENEMY_BALANCE); }
						EnemyTakeStep();
						if (RANDOM < 0.75) {
							var enemy_footsteps_trans:SoundTransform =
								new SoundTransform(ENEMY_VOLUME * enemy_footsteps_volume_adjustment[ENEMY_FOOTSTEPS_TYPE], ENEMY_BALANCE); 
							var enemy_footsteps_channel:SoundChannel = enemy_footsteps_sound[ENEMY_FOOTSTEPS_TYPE].play(
								0, 1, enemy_footsteps_trans);
						}
					}
					RANDOM = Math.random();
					if (RANDOM < 0.25) {
						var zombie_trans:SoundTransform = new SoundTransform(ENEMY_VOLUME * zombie_volume_adjustment[ENEMY_TYPE], ENEMY_BALANCE);
						var zombie_channel:SoundChannel = zombie_sound[ENEMY_TYPE].play(delay, loops, zombie_trans);
					}
				}
			}

			function reportKeyDown(event:KeyboardEvent):void { 
				var key:uint = event.keyCode;
				if (READY_FOR_INPUT == 1) {
					var moved:uint = 0;
					if (key == Keyboard.RIGHT) { PlayerTakeStep(-0.10, 0); }
					else if (key == Keyboard.LEFT) { PlayerTakeStep(0.10, 0); }
					else if (key == Keyboard.UP) { PlayerTakeStep(0, 0.10); }
					else if (key == Keyboard.DOWN) { PlayerTakeStep(0, -0.10); }
					else if ( (key >= 49) && (key <= 57) ) { SetDifficultyLevel(key - 48); }
					else if (key == Keyboard.SPACE) { CallToDamsel(); }
				}
			}

			function PlayPreInstructions():void {
				var pre_instructions_trans:SoundTransform = new SoundTransform(MIN_VOLUME * pre_instructions_volume_adjustment, 0); 
				var pre_instructions_channel:SoundChannel = pre_instructions_sound.play(0, 1, pre_instructions_trans);
			}

			function PlayInstructions():void {
				if (READY_FOR_INSTRUCTIONS == 1) {
					title_channel.stop();
					intro_music_channel.stop();
					background_music_channel.stop();
					var instructions_trans:SoundTransform = new SoundTransform(MIN_VOLUME * instructions_volume_adjustment, 0); 
					var instructions_channel:SoundChannel = instructions_sound.play(0, 1, instructions_trans);
					READY_FOR_INSTRUCTIONS = 0;
					INSTRUCTIONS_GRACE_PERIOD = 72;
					ENEMY_VOLUME = 0;
				}
			}

			function SetDifficultyLevel(xlevel:Number):void {
				DIFFICULTY_LEVEL = xlevel;
				var difficulty_trans:SoundTransform = new SoundTransform(MIN_VOLUME * difficulty_volume_adjustment, 0); 
				var difficulty_channel:SoundChannel = difficulty_sound[DIFFICULTY_LEVEL].play(0, 1, difficulty_trans);
				READY_FOR_INPUT = 0;
			}

			function PlayerTakeStep(xoffset:Number, yoffset:Number):void {
				if (PLAYER_LOCATION_Y - yoffset > MAX_VOLUME) { SoundFence(0, MAX_VOLUME, 0); }  // if trying to go off bottom of game map
				else if (PLAYER_LOCATION_Y - yoffset < MIN_VOLUME) { SoundFence(0, MAX_VOLUME, 0); }  // if trying to go off top of game map
				else {
					PLAYER_LOCATION_Y = PLAYER_LOCATION_Y - yoffset;
					// scroll enemy
					if (ENEMY_VOLUME > 0) {
						ENEMY_BALANCE = ENEMY_BALANCE + xoffset;  // scroll enemy horizontally
						ENEMY_VOLUME = ENEMY_VOLUME + yoffset;  // scroll enemy vertically
						if (ENEMY_VOLUME < MIN_VOLUME) { ENEMY_VOLUME = MIN_VOLUME; }
						if (ENEMY_VOLUME > MAX_VOLUME) { ENEMY_VOLUME = MAX_VOLUME; }
					}
					// scroll landscape
					for (i = 0; i <= NUM_MOVABLE_LANDSCAPE_ITEMS - 1; i++) {
						landscape_balance[i] = landscape_balance[i] + xoffset;  // scroll landscape items horizontally
						if (landscape_balance[i] > 2.0) { landscape_balance[i] = -2.0; } // if scrolled so far to the right, start over on left
						else if (landscape_balance[i] < -2.0) { landscape_balance[i] = 2.0; } // if scrolled so far to the left, start over on right
						landscape_volume[i] = landscape_volume[i] + yoffset;  // scroll landscape items vertically
					}
					// scroll damsel
					{
						DAMSEL_BALANCE = DAMSEL_BALANCE + xoffset;  // scroll damsel horizontally
						DAMSEL_VOLUME = DAMSEL_VOLUME + yoffset;  // scroll damsel vertically
						if (DAMSEL_VOLUME < MIN_VOLUME) { DAMSEL_VOLUME = MIN_VOLUME; }
						if (DAMSEL_VOLUME > MAX_VOLUME) { DAMSEL_VOLUME = MAX_VOLUME; }
					}
					// sound player footstep
					var footsteps_trans:SoundTransform = new SoundTransform(
						(MAX_VOLUME * footsteps_volume_adjustment) - (yoffset * 1), 0 - (xoffset * 10)); 
					var footsteps_channel:SoundChannel = footsteps_sound.play(0, 1, footsteps_trans);
				}
				READY_FOR_INPUT = 0;
			}

			function CheckMouseOnStage(e:MouseEvent):void {
				MOUSE_X = stage.mouseX;
				MOUSE_Y = stage.mouseY;
				if ( (MOUSE_X >= 1) && (MOUSE_X <= STAGE_WIDTH) && (MOUSE_Y >= 1) && (MOUSE_Y <= STAGE_HEIGHT) ) {
					MOUSE_ON_STAGE = 1;
					READY_FOR_MOUSE_ALERT = 1;
				}
				else if ( (MOUSE_X < 1) || (MOUSE_X > STAGE_WIDTH) || (MOUSE_Y < 1) || (MOUSE_Y > STAGE_HEIGHT) ) {
					MOUSE_ON_STAGE = 0;
					if (READY_FOR_MOUSE_ALERT == 1) {
						SoundPlink();
						READY_FOR_MOUSE_ALERT = 0;
					}
				}
			}


		}

		// Play intro music once loaded
		protected function soundLoadCompleteHandler1(evt : Event):void {
			var title_trans:SoundTransform = new SoundTransform(MIN_VOLUME * title_volume_adjustment, 0); 
			title_channel = title_sound.play(0, 1, title_trans);
			var intro_music_trans:SoundTransform = new SoundTransform(MIN_VOLUME * intro_music_volume_adjustment, 0); 
			intro_music_channel = intro_music_sound.play(0, 1, intro_music_trans);
		}

		// Play background music once loaded
		protected function soundLoadCompleteHandler2(evt : Event):void {
			var background_music_trans:SoundTransform = new SoundTransform(MIN_VOLUME * background_music_volume_adjustment, 0); 
			background_music_channel = background_music_sound.play(0, 999, background_music_trans);
		}

		protected function loadError(evt : IOErrorEvent):void { trace ("ERROR :: " + evt); }  // when error detected
	}
}
