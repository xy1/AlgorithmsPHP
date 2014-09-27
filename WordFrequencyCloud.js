/*
 *
 * Generate word cloud based on frequencies of words in current document.
 *
 * This was a timed assignment that I raced to complete in under 3 hours.
 * It works, but if I had more time, I would have wrapped it in a JavaScript prototype class.
 * For some reason, I also struggled with manipluating multi-dimensional arrays in JavaScript
 * so I had to split the results into 2 parallel arrays.
 *
 * Assumptions:
 * - Ignore all words under 4 characters in length.
 * - HTML tags and JavaScript will be included in the analysis.
 * - Words will be treated case-insensitively.
 * - All compound words that contain hyphens, apostrophes, or other punctuation will be treated as separate words.
 * - Plural and singular forms of a word will be treated as separate words.
 *
 */

function () {

	var i;

	/* read current page and store in string variable */
	var text = document.documentElement.innerHTML;

	/* convert to lower case */
	text = text.toLowerCase();

	/* strip non-alphabetic characters */
	var this_char;
	var temp_text = '';
	for (i = 0; i < text.length; i++) {
		this_char = text.substr(i, 1);
		if (this_char.charCodeAt(0) >= 97 && this_char.charCodeAt(0) <= 122) temp_text += this_char;
		else temp_text += ' ';
	}
	text = temp_text;

	/* remove duplicate spaces */
	while (text.search('  ') >= 0) {
		text = text.replace(/  /g,' ');
	}

	/* trim any leading or trailing spaces */
	text = text.trim();

	/* parse words, store in array */
	var words = [];
	var this_word;
	var last_position = 0;
	var word_count = 0;
	for (i = 0; i < text.length; i++) {
		this_char = text.substr(i, 1);
		if (this_char == ' ') {
			this_word = text.substring(last_position, i);
			this_word = this_word.trim();
			if (this_word.length >= 4) {
				words[word_count] = this_word;
				word_count++;
			}
			last_position = i;
		}
	}

	/* sort the words */
	words.sort();

	/* calculate word frequencies, store results in arrays for listing */
	var word_frequencies = [];
	var words_final = [];
	var frequency = 0;
	var last_word = '';
	word_count = 0;
	for (i = 0; i < words.length; i++) {
		frequency++;
		if (words[i] != last_word) {
			words_final[word_count] = last_word;
			word_frequencies[word_count] = frequency;
			word_count++;
			frequency = 0;
			last_word = words[i];
		}
	}

	/* display words and frequencies in word cloud style */
	var font_size;
	for (i = 0; i < words_final.length; i++) {
		font_size = 10 + (word_frequencies[i] * 2);  /* calculate varying font size based on frequency */
		document.write('<span style=\'font-size: ' + font_size + 'px;\'>');
		document.write(words_final[i] + ' ');
		document.write('</span>');
		document.write(' ');
	}


}();
