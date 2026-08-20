<?php
/**
 * Zlaark Homepage - the whole homepage as one widget.
 *
 * Rather than assembling eight widgets by hand and keeping their settings in
 * sync, this renders the full eight-section architecture from a single drop:
 * hero, scorecard, the dark methodology band, live deals, editor's picks, the
 * logo marquee and a closing call to action.
 *
 * Every section has a toggle, and every deal-driven section reads the same
 * catalogue, so the numbers on the page can never disagree with each other.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class Zlaark_Homepage_Widget extends Zlaark_Query_Widget_Base {

	public function get_name() {
		return 'zlaark_homepage';
	}

	public function get_title() {
		return __( 'Zlaark Homepage', 'zlaark-deals-pro' );
	}

	public function get_icon() {
		return 'eicon-single-page';
	}

	public function get_keywords() {
		return array( 'homepage', 'home', 'template', 'landing', 'all in one' );
	}

	protected function register_controls() {
		$this->controls_hero();
		$this->controls_scorecard();
		$this->controls_band();
		$this->controls_deals();
		$this->controls_picks();
		$this->controls_mq();
		$this->controls_cats();
		$this->controls_exp();
		$this->controls_method();
		$this->controls_about();
		$this->controls_faq();
		$this->controls_cta();
		$this->controls_shared();
	}

	protected function controls_hero() {
		/* ------------------------------------------------------ 01 hero */

		$this->start_controls_section(
			'sec_hero',
			array( 'label' => __( '01 · Hero', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_hero', $this->toggle( true ) );

		$this->add_control(
			'hero_eyebrow',
			array(
				'label'       => __( 'Eyebrow', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Bought with our own money', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_title',
			array(
				'label'       => __( 'Headline', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( 'Save on the tools you already pay for', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_highlight',
			array(
				'label'       => __( 'Highlight Phrase', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'already pay for', 'zlaark-deals-pro' ),
				'label_block' => true,
				'description' => __( 'Rendered in the accent colour where it appears in the headline.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_text',
			array(
				'label'       => __( 'Sub-line', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'Hand-tested discounts on hosting, ecommerce platforms and the software your team runs on. Scored out of ten, compared, and re-checked every month.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_cta_text',
			array(
				'label'     => __( 'Button Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Browse all deals', 'zlaark-deals-pro' ),
				'condition' => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_cta_url',
			array(
				'label'       => __( 'Button URL', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/deals/',
				'condition'   => array( 'show_hero' => 'yes' ),
			)
		);

		$this->add_control(
			'hero_board',
			array(
				'label'        => __( 'Savings Scoreboard', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'The four biggest current savings, computed from your deals. Replaces a stock hero image with something only you can show.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_hero' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_scorecard() {
		/* ------------------------------------------------ 02 scorecard */

		$this->start_controls_section(
			'sec_score',
			array( 'label' => __( '02 · Scorecard', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_score', $this->toggle( true ) );

		$this->add_control(
			'score_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( "The four we'd actually pay for", 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_score' => 'yes' ),
			)
		);

		$this->add_control(
			'score_count',
			array(
				'label'     => __( 'How Many', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 4,
				'default'   => 4,
				'condition' => array( 'show_score' => 'yes' ),
			)
		);

		$this->add_control(
			'score_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Columns are capped with each deal\'s <strong>Rank Label</strong> - set one per deal ("Best overall", "Best value").', 'zlaark-deals-pro' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array( 'show_score' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_band() {
		/* ---------------------------------------------- 03 methodology */

		$this->start_controls_section(
			'sec_band',
			array( 'label' => __( '03 · Methodology band', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_band', $this->toggle( true ) );

		$this->add_control(
			'band_eyebrow',
			array(
				'label'     => __( 'Eyebrow', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Bought with our own money', 'zlaark-deals-pro' ),
				'condition' => array( 'show_band' => 'yes' ),
			)
		);

		$this->add_control(
			'band_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'We buy every deal with our own money', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_band' => 'yes' ),
			)
		);

		$this->add_control(
			'band_text',
			array(
				'label'     => __( 'Body', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 4,
				'default'   => __( 'No vendor sends us a free account. We pay, we run a real site on it for at least thirty days, we measure speed and uptime ourselves, and we print the renewal price the vendor would rather you found out later.', 'zlaark-deals-pro' ),
				'condition' => array( 'show_band' => 'yes' ),
			)
		);

		$stats = new Repeater();
		$stats->add_control(
			'value',
			array(
				'label'   => __( 'Number', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '102',
			)
		);
		$stats->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'zlaark-deals-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'deals bought and tested', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'band_stats',
			array(
				'label'       => __( 'Receipts', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $stats->get_controls(),
				'title_field' => '{{{ value }}} - {{{ label }}}',
				'condition'   => array( 'show_band' => 'yes' ),
				'default'     => array(
					array( 'value' => '102', 'label' => __( 'deals bought and tested', 'zlaark-deals-pro' ) ),
					array( 'value' => '$8,400', 'label' => __( 'spent testing this year', 'zlaark-deals-pro' ) ),
					array( 'value' => '14', 'label' => __( 'providers dropped this year', 'zlaark-deals-pro' ) ),
					array( 'value' => '30', 'label' => __( 'day minimum test period', 'zlaark-deals-pro' ) ),
				),
			)
		);

		$this->add_control(
			'band_auto',
			array(
				'label'        => __( 'Use Live Deal Count', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Replaces the first number with the real count of live deals, so it can never go stale.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_band' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_deals() {
		/* -------------------------------------------------- 04 deals */

		$this->start_controls_section(
			'sec_deals',
			array( 'label' => __( '04 · Live deals', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_deals', $this->toggle( true ) );

		$this->add_control(
			'deals_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Every offer, verified this month', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_deals' => 'yes' ),
			)
		);

		$this->add_control(
			'deals_count',
			array(
				'label'     => __( 'How Many', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 3,
				'max'       => 24,
				'default'   => 12,
				'condition' => array( 'show_deals' => 'yes' ),
			)
		);

		$this->add_control(
			'deals_more_text',
			array(
				'label'     => __( 'Link Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'See all deals', 'zlaark-deals-pro' ),
				'condition' => array( 'show_deals' => 'yes' ),
			)
		);

		$this->add_control(
			'deals_more_url',
			array(
				'label'       => __( 'Link URL', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/deals/',
				'condition'   => array( 'show_deals' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_picks() {
		/* -------------------------------------------------- 05 picks */

		$this->start_controls_section(
			'sec_picks',
			array( 'label' => __( '05 · Editor\'s picks', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_picks', $this->toggle( true ) );

		$this->add_control(
			'picks_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( "Editor's picks", 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_picks' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_mq() {
		/* ------------------------------------------------ 06 marquee */

		$this->start_controls_section(
			'sec_mq',
			array( 'label' => __( '09 · Trusted by', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_mq', $this->toggle( true ) );

		$this->end_controls_section();
	}

	protected function controls_cats() {
		/* ------------------------------------------- 06b categories */

		$this->start_controls_section(
			'sec_cats',
			array( 'label' => __( '06 · Browse by category', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_cats', $this->toggle( true ) );

		$this->add_control(
			'cats_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Browse by category', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_cats' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_exp() {
		/* -------------------------------------------- 06c expiring */

		$this->start_controls_section(
			'sec_exp',
			array( 'label' => __( '07 · Expiring this week', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_exp', $this->toggle( true ) );

		$this->add_control(
			'exp_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Ending soon', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_exp' => 'yes' ),
			)
		);

		$this->add_control(
			'exp_days',
			array(
				'label'       => __( 'Within How Many Days', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 30,
				'default'     => 14,
				'description' => __( 'The section hides itself when nothing is expiring, so it can never show a stale countdown.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_exp' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_method() {
		/* ------------------------------------------- 06d how we test */

		$this->start_controls_section(
			'sec_method',
			array( 'label' => __( '08 · How we test', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_method', $this->toggle( true ) );

		$this->add_control(
			'method_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'How we test', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_method' => 'yes' ),
			)
		);

		$steps = new Repeater();
		$steps->add_control(
			'title',
			array( 'label' => __( 'Step', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'We buy it', 'zlaark-deals-pro' ) )
		);
		$steps->add_control(
			'text',
			array( 'label' => __( 'Detail', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2 )
		);

		$this->add_control(
			'method_steps',
			array(
				'label'       => __( 'Steps', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $steps->get_controls(),
				'title_field' => '{{{ title }}}',
				'condition'   => array( 'show_method' => 'yes' ),
				'default'     => array(
					array(
						'title' => __( 'We buy it', 'zlaark-deals-pro' ),
						'text'  => __( 'With our own money, on the plan a real customer would pick. No vendor accounts, no press seats.', 'zlaark-deals-pro' ),
					),
					array(
						'title' => __( 'We run a real site on it', 'zlaark-deals-pro' ),
						'text'  => __( 'For at least thirty days, so the numbers come from use rather than a trial dashboard.', 'zlaark-deals-pro' ),
					),
					array(
						'title' => __( 'We measure it ourselves', 'zlaark-deals-pro' ),
						'text'  => __( 'Speed and uptime from our own monitoring, not from the vendor\'s marketing page.', 'zlaark-deals-pro' ),
					),
					array(
						'title' => __( 'We re-check every month', 'zlaark-deals-pro' ),
						'text'  => __( 'Prices move and offers lapse. A deal that stops being true stops being listed.', 'zlaark-deals-pro' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_about() {
		/* --------------------------------------------- 06f about us */

		$this->start_controls_section(
			'sec_about',
			array( 'label' => __( '10 · About us', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_about', $this->toggle( true ) );

		$this->add_control(
			'about_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'The people who actually test this', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_about' => 'yes' ),
			)
		);

		$this->add_control(
			'about_text',
			array(
				'label'     => __( 'Intro', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => __( 'Small team, no outsourced reviews. Every score on this site was produced by one of us, on an account we paid for.', 'zlaark-deals-pro' ),
				'condition' => array( 'show_about' => 'yes' ),
			)
		);

		$people = new Repeater();
		$people->add_control(
			'photo',
			array( 'label' => __( 'Photo', 'zlaark-deals-pro' ), 'type' => Controls_Manager::MEDIA )
		);
		$people->add_control(
			'name',
			array( 'label' => __( 'Name', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Name', 'zlaark-deals-pro' ) )
		);
		$people->add_control(
			'role',
			array( 'label' => __( 'Role', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Reviewer', 'zlaark-deals-pro' ) )
		);
		$people->add_control(
			'line',
			array(
				'label'       => __( 'One Line', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => __( 'What they test, or how long they have done it. Specific beats flattering.', 'zlaark-deals-pro' ),
			)
		);

		$this->add_control(
			'about_people',
			array(
				'label'       => __( 'People', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $people->get_controls(),
				'title_field' => '{{{ name }}} - {{{ role }}}',
				'condition'   => array( 'show_about' => 'yes' ),
				'default'     => array(
					array(
						'name' => __( 'Kanish Kumar', 'zlaark-deals-pro' ),
						'role' => __( 'Editor', 'zlaark-deals-pro' ),
						'line' => __( 'Buys and benchmarks every hosting plan on the site. Testing since 2010.', 'zlaark-deals-pro' ),
					),
					array(
						'name' => __( 'Add a second reviewer', 'zlaark-deals-pro' ),
						'role' => __( 'Researcher', 'zlaark-deals-pro' ),
						'line' => __( 'Two names carry more weight than one. Replace or remove this entry.', 'zlaark-deals-pro' ),
					),
				),
			)
		);

		$this->add_control(
			'about_cta_text',
			array(
				'label'     => __( 'Link Text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'How we test', 'zlaark-deals-pro' ),
				'condition' => array( 'show_about' => 'yes' ),
			)
		);

		$this->add_control(
			'about_cta_url',
			array(
				'label'     => __( 'Link URL', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_about' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_faq() {
		/* ------------------------------------------------- 06e faq */

		$this->start_controls_section(
			'sec_faq',
			array( 'label' => __( '11 · Questions', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_faq', $this->toggle( true ) );

		$this->add_control(
			'faq_title',
			array(
				'label'       => __( 'Heading', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Questions we get asked', 'zlaark-deals-pro' ),
				'label_block' => true,
				'condition'   => array( 'show_faq' => 'yes' ),
			)
		);

		$faq = new Repeater();
		$faq->add_control(
			'q',
			array( 'label' => __( 'Question', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXT, 'label_block' => true )
		);
		$faq->add_control(
			'a',
			array( 'label' => __( 'Answer', 'zlaark-deals-pro' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3 )
		);

		$this->add_control(
			'faq_items',
			array(
				'label'       => __( 'Questions', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $faq->get_controls(),
				'title_field' => '{{{ q }}}',
				'condition'   => array( 'show_faq' => 'yes' ),
				'default'     => array(
					array(
						'q' => __( 'Do you get paid for these deals?', 'zlaark-deals-pro' ),
						'a' => __( 'Some links earn us a commission if you buy through them, at no extra cost to you. It never changes a score, and we buy every product we test.', 'zlaark-deals-pro' ),
					),
					array(
						'q' => __( 'How current are the prices?', 'zlaark-deals-pro' ),
						'a' => __( 'Every deal carries the date we last checked it, and anything with an expiry date drops off the site the day it lapses.', 'zlaark-deals-pro' ),
					),
					array(
						'q' => __( 'Why do you show the renewal price?', 'zlaark-deals-pro' ),
						'a' => __( 'Because the intro price is only half the story. Most sites leave it out; we would rather you knew what you are committing to.', 'zlaark-deals-pro' ),
					),
				),
			)
		);

		$this->add_control(
			'faq_schema',
			array(
				'label'        => __( 'Emit FAQ Markup', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Adds FAQPage structured data so the questions can appear directly in search results.', 'zlaark-deals-pro' ),
				'condition'    => array( 'show_faq' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_cta() {
		/* ---------------------------------------------------- 07 cta */

		$this->start_controls_section(
			'sec_cta',
			array( 'label' => __( '12 · Closing CTA', 'zlaark-deals-pro' ) )
		);

		$this->add_control( 'show_cta', $this->toggle( true ) );

		$this->add_control(
			'cta_title',
			array(
				'label'     => __( 'Heading', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Deal alerts', 'zlaark-deals-pro' ),
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'Body', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'New deals, verified monthly. No affiliate spam.', 'zlaark-deals-pro' ),
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_shortcode',
			array(
				'label'       => __( 'Form Shortcode', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'placeholder' => '[your_form_shortcode]',
				'description' => __( 'Any signup form shortcode. Leave empty to show the heading and body only.', 'zlaark-deals-pro' ),
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'nofollow',
			array(
				'label'        => __( 'Mark Links Sponsored', 'zlaark-deals-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'description'  => __( 'Adds rel="nofollow sponsored" to every affiliate link on the page. Required disclosure for affiliate links.', 'zlaark-deals-pro' ),
			)
		);

		$this->end_controls_section();
	}

	protected function controls_shared() {
		/* -------------------------------------------------- source */

		$this->query_controls( 12 );

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout & colour', 'zlaark-deals-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->max_width_control( '{{WRAPPER}} .zd-home__inner' );

		$this->add_control(
			'c_accent',
			array(
				'label'     => __( 'Accent', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .zd-home' => '--zd-accent: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_accent_2',
			array(
				'label'     => __( 'Accent (hover)', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-home' => '--zd-accent-2: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_accent_tint',
			array(
				'label'       => __( 'Accent tint', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Ground behind savings pills and score badges. Keep it pale - text sits on it.', 'zlaark-deals-pro' ),
				'selectors'   => array( '{{WRAPPER}} .zd-home' => '--zd-accent-tint: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_ember',
			array(
				'label'       => __( 'Urgency', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Countdowns and expiry flags only.', 'zlaark-deals-pro' ),
				'selectors'   => array( '{{WRAPPER}} .zd-home' => '--zd-ember: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_ink',
			array(
				'label'       => __( 'Dark band', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Ground for the methodology band and the savings readout.', 'zlaark-deals-pro' ),
				'selectors'   => array( '{{WRAPPER}} .zd-home' => '--zd-ink: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_heading',
			array(
				'label'     => __( 'Headings', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-home' => '--zd-heading: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_body',
			array(
				'label'     => __( 'Body text', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-home' => '--zd-body: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_surface',
			array(
				'label'       => __( 'Tinted sections', 'zlaark-deals-pro' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'Ground for the alternating sections and logo tiles.', 'zlaark-deals-pro' ),
				'selectors'   => array( '{{WRAPPER}} .zd-home' => '--zd-surface: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'c_hairline',
			array(
				'label'     => __( 'Borders', 'zlaark-deals-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .zd-home' => '--zd-hairline: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'sec_pad',
			array(
				'label'      => __( 'Section spacing', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 140 ) ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .zd-home__sec' => 'padding-top: {{SIZE}}px; padding-bottom: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Card corner radius', 'zlaark-deals-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array( '{{WRAPPER}} .zd-home' => '--zd-r-md: {{SIZE}}px;' ),
			)
		);

		$this->end_controls_section();

		$this->animation_controls( false );
	}

	protected function toggle( $on ) {
		return array(
			'label'        => __( 'Show This Section', 'zlaark-deals-pro' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => $on ? 'yes' : '',
			'return_value' => 'yes',
		);
	}

	/** One query feeds every section, so no two blocks can disagree. */
	protected function fetch( $s ) {
		$args                   = $this->build_query_args( $s );
		$args['posts_per_page'] = 60;

		$query = new WP_Query( $args );
		$deals = array();
		$seen  = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$deal = Zlaark_Deals_Meta::get_deal_data( get_post() );
			if ( empty( $deal ) ) {
				continue;
			}

			/*
			 * Catalogues pick up near-duplicates ("DreamHost" and "DreamHost
			 * Special"). Without scores every deal ties, so the same brand can
			 * win several places in one row. Keep the first of each name.
			 */
			$key = strtolower( trim( $deal['title'] ) );
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$seen[ $key ] = true;

			$deals[] = $deal;
		}
		wp_reset_postdata();

		return $deals;
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$deals = $this->fetch( $s );

		if ( empty( $deals ) ) {
			$this->render_empty_notice();
			return;
		}

		// Biggest saving first - the ordering the whole page argues from.
		$by_saving = $deals;
		usort(
			$by_saving,
			function ( $a, $b ) {
				$x = ( null === $a['discount_pct'] ) ? -1 : (int) $a['discount_pct'];
				$y = ( null === $b['discount_pct'] ) ? -1 : (int) $b['discount_pct'];
				return $y - $x;
			}
		);

		$by_score = $deals;
		usort(
			$by_score,
			function ( $a, $b ) {
				$x = ( null === $a['overall_score'] ) ? -1 : (float) $a['overall_score'];
				$y = ( null === $b['overall_score'] ) ? -1 : (float) $b['overall_score'];
				return ( $y > $x ) ? 1 : ( ( $y < $x ) ? -1 : 0 );
			}
		);
		?>
		<div class="zd-home" data-zd-reveal-root="true" data-zd-stagger="60">
			<?php
			if ( 'yes' === $s['show_hero'] ) {
				$this->section_hero( $s, $by_saving );
			}
			if ( 'yes' === $s['show_score'] ) {
				$this->section_scorecard( $s, $by_score );
			}
			if ( 'yes' === $s['show_band'] ) {
				$this->section_band( $s, $deals );
			}
			if ( 'yes' === $s['show_deals'] ) {
				$this->section_deals( $s, $by_saving );
			}
			if ( 'yes' === $s['show_picks'] ) {
				$this->section_picks( $s, $by_score );
			}
			if ( 'yes' === $s['show_cats'] ) {
				$this->section_categories( $s, $deals );
			}
			if ( 'yes' === $s['show_exp'] ) {
				$this->section_expiring( $s, $deals );
			}
			if ( 'yes' === $s['show_method'] ) {
				$this->section_method( $s );
			}
			if ( 'yes' === $s['show_about'] ) {
				$this->section_about( $s );
			}
			if ( 'yes' === $s['show_faq'] ) {
				$this->section_faq( $s );
			}
			if ( 'yes' === $s['show_mq'] ) {
				$this->section_marquee( $deals );
			}
			if ( 'yes' === $s['show_cta'] ) {
				$this->section_cta( $s );
			}
			?>
		</div>
		<?php
	}

	/** Wraps the highlight phrase in an accent span, once. */
	private function highlight( $title, $phrase ) {
		$title = esc_html( $title );
		if ( '' === trim( (string) $phrase ) ) {
			return $title;
		}
		$phrase = esc_html( $phrase );
		$pos    = strpos( $title, $phrase );
		if ( false === $pos ) {
			return $title;
		}
		return substr( $title, 0, $pos )
			. '<em class="zd-home__hl">' . $phrase . '</em>'
			. substr( $title, $pos + strlen( $phrase ) );
	}

	/* ---------------------------------------------------- 01 hero */

	protected function section_hero( $s, $deals ) {
		$url = ! empty( $s['hero_cta_url']['url'] ) ? $s['hero_cta_url']['url'] : '';
		$top = array_slice(
			array_values(
				array_filter(
					$deals,
					function ( $d ) {
						return null !== $d['discount_pct'];
					}
				)
			),
			0,
			4
		);
		?>
		<section class="zd-home__sec zd-home__hero">
			<div class="zd-home__inner zd-home__herogrid">
				<div class="zd-reveal" data-zd-reveal="rise">
					<?php if ( '' !== $s['hero_eyebrow'] ) : ?>
						<span class="zd-chip zd-chip--brand"><?php echo esc_html( $s['hero_eyebrow'] ); ?></span>
					<?php endif; ?>
					<h1 class="zd-home__h1">
						<?php echo wp_kses_post( $this->highlight( $s['hero_title'], $s['hero_highlight'] ) ); ?>
					</h1>
					<?php if ( '' !== $s['hero_text'] ) : ?>
						<p class="zd-home__lede"><?php echo esc_html( $s['hero_text'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $s['hero_cta_text'] && '' !== $url ) : ?>
						<a class="zd-btn zd-btn--solid zd-home__herocta" href="<?php echo esc_url( $url ); ?>">
							<span class="zd-btn__label"><?php echo esc_html( $s['hero_cta_text'] ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( 'yes' === $s['hero_board'] && ! empty( $top ) ) : ?>
					<div class="zd-home__board zd-reveal" data-zd-reveal="rise">
						<div class="zd-home__boardhead">
							<b><?php esc_html_e( 'Biggest savings right now', 'zlaark-deals-pro' ); ?></b>
							<span class="zd-home__live"><?php esc_html_e( 'live', 'zlaark-deals-pro' ); ?></span>
						</div>
						<?php foreach ( $top as $deal ) : ?>
							<div class="zd-home__boardrow">
								<span class="zd-home__boardlogo">
									<?php if ( $deal['image_id'] ) : ?>
										<?php echo wp_get_attachment_image( $deal['image_id'], 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<?php echo esc_html( mb_substr( $deal['title'], 0, 2 ) ); ?>
									<?php endif; ?>
								</span>
								<span class="zd-home__boardname">
									<b><?php echo esc_html( $deal['title'] ); ?></b>
									<?php if ( '' !== $deal['old_price'] && '' !== $deal['price'] ) : ?>
										<span><?php echo esc_html( $deal['old_price'] . ' → ' . $deal['price'] ); ?></span>
									<?php endif; ?>
								</span>
								<span class="zd-home__boardpc">−<?php echo esc_html( (int) $deal['discount_pct'] ); ?>%</span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/* ----------------------------------------------- 02 scorecard */

	protected function section_scorecard( $s, $deals ) {
		$picks = array_slice( $deals, 0, max( 2, (int) $s['score_count'] ) );
		if ( count( $picks ) < 2 ) {
			return;
		}
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['score_title'], __( 'Scored and re-checked monthly', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-home__cards zd-home__cards--4">
					<?php foreach ( $picks as $i => $deal ) : ?>
						<article class="zd-card zd-card--pick zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
							<?php if ( '' !== $deal['rank_label'] ) : ?>
								<span class="zd-card__cap<?php echo 0 === $i ? ' zd-card__cap--lead' : ''; ?>">
									<?php echo esc_html( $deal['rank_label'] ); ?>
								</span>
							<?php endif; ?>

							<?php $this->card_brand( $deal ); ?>
							<?php $this->card_price( $deal ); ?>

							<div class="zd-card__slot">
								<?php $this->card_body( $deal, 4 ); ?>
							</div>

							<?php $this->card_terms( $deal ); ?>

							<div class="zd-card__foot">
								<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-home__block' ); ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------- 03 methodology */

	protected function section_band( $s, $deals ) {
		$stats = is_array( $s['band_stats'] ) ? $s['band_stats'] : array();

		// The first figure comes from the live catalogue, so it cannot go stale.
		if ( 'yes' === $s['band_auto'] && ! empty( $stats ) ) {
			$stats[0]['value'] = number_format_i18n( count( $deals ) );
		}
		?>
		<section class="zd-home__sec zd-home__band">
			<div class="zd-home__inner">
				<?php if ( '' !== $s['band_eyebrow'] ) : ?>
					<p class="zd-home__eyebrow zd-reveal" data-zd-reveal="rise"><?php echo esc_html( $s['band_eyebrow'] ); ?></p>
				<?php endif; ?>
				<h2 class="zd-home__bandtitle zd-reveal" data-zd-reveal="rise"><?php echo esc_html( $s['band_title'] ); ?></h2>
				<?php if ( '' !== $s['band_text'] ) : ?>
					<p class="zd-home__bandtext zd-reveal" data-zd-reveal="rise"><?php echo esc_html( $s['band_text'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $stats ) ) : ?>
					<div class="zd-home__receipts">
						<?php foreach ( $stats as $i => $row ) : ?>
							<div class="zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
								<b><?php echo esc_html( $row['value'] ); ?></b>
								<span><?php echo esc_html( $row['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------- 04 deals */

	protected function section_deals( $s, $deals ) {
		$show = array_slice( $deals, 0, max( 3, (int) $s['deals_count'] ) );
		$url  = ! empty( $s['deals_more_url']['url'] ) ? $s['deals_more_url']['url'] : '';
		?>
		<section class="zd-home__sec zd-home__tint">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['deals_title'], __( 'Every offer we currently rate', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-home__cards zd-home__cards--3">
					<?php foreach ( $show as $i => $deal ) : ?>
						<?php $this->mini_card( $deal, $s, $i ); ?>
					<?php endforeach; ?>
				</div>
				<?php if ( '' !== $s['deals_more_text'] && '' !== $url ) : ?>
					<p class="zd-home__more">
						<a class="zd-btn zd-btn--ghost" href="<?php echo esc_url( $url ); ?>">
							<span class="zd-btn__label"><?php echo esc_html( $s['deals_more_text'] ); ?></span>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------- 05 picks */

	protected function section_picks( $s, $deals ) {
		$picks = array_slice( $deals, 0, 3 );
		if ( count( $picks ) < 2 ) {
			return;
		}
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['picks_title'], __( 'Ranked by measured score', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-home__cards zd-home__cards--3">
					<?php foreach ( $picks as $i => $deal ) : ?>
						<article class="zd-card zd-card--rank zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
							<span class="zd-card__rank"><?php echo esc_html( $i + 1 ); ?></span>

							<?php $this->card_brand( $deal ); ?>
							<?php $this->card_price( $deal ); ?>

							<div class="zd-card__slot">
								<?php $this->card_body( $deal, 4 ); ?>
							</div>

							<?php $this->card_terms( $deal ); ?>

							<div class="zd-card__foot">
								<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-home__block' ); ?>
								<?php if ( '' !== $deal['review_url'] ) : ?>
									<a class="zd-card__review" href="<?php echo esc_url( $deal['review_url'] ); ?>">
										<?php esc_html_e( 'Read the full review', 'zlaark-deals-pro' ); ?>
									</a>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* ------------------------------------------------- 06 marquee */

	protected function section_marquee( $deals ) {
		$logos = array_values(
			array_filter(
				$deals,
				function ( $d ) {
					return ! empty( $d['image_id'] );
				}
			)
		);

		if ( count( $logos ) < 3 ) {
			return;
		}
		?>
		<section class="zd-home__sec zd-home__mq zd-marquee zd-marquee--fade zd-marquee--gray zd-marquee--pause"
			style="--zd-marquee-speed:34s;">
			<div class="zd-marquee__viewport">
				<div class="zd-marquee__track">
					<?php foreach ( $logos as $deal ) : ?>
						<span class="zd-marquee__item">
							<?php echo wp_get_attachment_image( $deal['image_id'], 'medium', false, array( 'loading' => 'lazy' ) ); ?>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* ----------------------------------------------------- 07 cta */

	protected function section_cta( $s ) {
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<div class="zd-home__cta zd-reveal" data-zd-reveal="rise">
					<div>
						<h2><?php echo esc_html( $s['cta_title'] ); ?></h2>
						<?php if ( '' !== $s['cta_text'] ) : ?>
							<p><?php echo esc_html( $s['cta_text'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( '' !== trim( (string) $s['cta_shortcode'] ) ) : ?>
						<div class="zd-home__ctaform">
							<?php echo do_shortcode( wp_kses_post( $s['cta_shortcode'] ) ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* -------------------------------------------------- fragments */

	protected function section_head( $title, $eyebrow ) {
		if ( '' === trim( (string) $title ) ) {
			return;
		}
		?>
		<div class="zd-home__head zd-reveal" data-zd-reveal="rise">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="zd-home__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		</div>
		<?php
	}

	/**
	 * The middle of a card: score bars if we have them, else the highlights
	 * checklist, else the tagline. Something always fills this slot, which is
	 * what stops a thin deal rendering as an empty gap.
	 *
	 * @return string One of bars|ticks|text|'' so the caller can decide layout.
	 */
	protected function card_body( $deal, $max_bars = 4 ) {
		$bars = array();
		if ( ! empty( $deal['scores'] ) ) {
			foreach ( $deal['scores'] as $row ) {
				if ( null !== $row['value'] ) {
					$bars[] = $row;
				}
			}
		}

		if ( ! empty( $bars ) ) {
			echo '<div class="zd-cbody zd-cbody--bars">';
			foreach ( array_slice( $bars, 0, $max_bars ) as $i => $row ) {
				$band = Zlaark_Deals_Computed::score_band( $row['value'] );
				printf(
					'<div class="zd-cbar" style="--zd-i:%d"><span>%s</span>'
					. '<span class="zd-cbar__track"><i class="zd-cbar__fill zd-fill--%s" data-zd-bar="%s"></i></span>'
					. '<b class="zd-score--%s">%s</b></div>',
					(int) $i,
					esc_html( $row['label'] ),
					esc_attr( $band ),
					esc_attr( $row['value'] * 10 ),
					esc_attr( $band ),
					esc_html( number_format_i18n( $row['value'], 1 ) )
				);
			}
			echo '</div>';
			return 'bars';
		}

		if ( ! empty( $deal['highlights'] ) ) {
			echo '<ul class="zd-cbody zd-cbody--ticks">';
			foreach ( array_slice( $deal['highlights'], 0, $max_bars ) as $line ) {
				echo '<li>' . esc_html( $line ) . '</li>';
			}
			echo '</ul>';
			return 'ticks';
		}

		if ( '' !== $deal['tagline'] ) {
			echo '<p class="zd-cbody zd-cbody--text">' . esc_html( $deal['tagline'] ) . '</p>';
			return 'text';
		}

		return '';
	}

	/** Price, struck original and the savings, kept together on one line. */
	protected function card_price( $deal ) {
		if ( '' === $deal['price'] && '' === $deal['offer_headline'] ) {
			return;
		}
		?>
		<p class="zd-cprice">
			<?php if ( '' !== $deal['price'] ) : ?>
				<b><?php echo esc_html( $deal['price'] ); ?></b>
			<?php else : ?>
				<b class="zd-cprice--word"><?php echo esc_html( $deal['offer_headline'] ); ?></b>
			<?php endif; ?>
			<?php if ( '' !== $deal['old_price'] ) : ?>
				<s><?php echo esc_html( $deal['old_price'] ); ?></s>
			<?php endif; ?>
			<?php if ( null !== $deal['discount_pct'] ) : ?>
				<em>
					<?php
					printf(
						/* translators: %d: discount percentage, rounded down. */
						esc_html__( 'Save %d%%', 'zlaark-deals-pro' ),
						(int) $deal['discount_pct']
					);
					?>
				</em>
			<?php endif; ?>
		</p>
		<?php
	}

	/** Logo, name, one meta line, and the score anchoring the right edge. */
	protected function card_brand( $deal ) {
		$types = Zlaark_Deals_Meta::offer_types();
		$type  = ( '' !== $deal['offer_type'] && isset( $types[ $deal['offer_type'] ] ) )
			? $types[ $deal['offer_type'] ]
			: '';

		$meta = array();
		if ( '' !== $type ) {
			$meta[] = $type;
		}
		if ( '' !== $deal['tested_date'] ) {
			$meta[] = sprintf(
				/* translators: %s: month and year tested. */
				__( 'Tested %s', 'zlaark-deals-pro' ),
				date_i18n( 'M Y', strtotime( $deal['tested_date'] ) )
			);
		}
		?>
		<header class="zd-cbrand">
			<span class="zd-cbrand__logo">
				<?php if ( $deal['image_id'] ) : ?>
					<?php echo wp_get_attachment_image( $deal['image_id'], 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<i aria-hidden="true"><?php echo esc_html( mb_substr( $deal['title'], 0, 2 ) ); ?></i>
				<?php endif; ?>
			</span>
			<span class="zd-cbrand__id">
				<h3><?php echo esc_html( $deal['title'] ); ?></h3>
				<?php if ( ! empty( $meta ) ) : ?>
					<span><?php echo esc_html( implode( ' · ', $meta ) ); ?></span>
				<?php endif; ?>
			</span>
			<?php if ( null !== $deal['overall_score'] ) : ?>
				<span class="zd-cscore zd-score--<?php echo esc_attr( $deal['score_band'] ); ?>">
					<b><?php echo esc_html( number_format_i18n( $deal['overall_score'], 1 ) ); ?></b>
					<i><?php esc_html_e( 'score', 'zlaark-deals-pro' ); ?></i>
				</span>
			<?php endif; ?>
		</header>
		<?php
	}

	/** Renewal, first-term total, refund window and the verification line. */
	protected function card_terms( $deal ) {
		$terms = array();

		if ( '' !== $deal['renewal_price'] ) {
			$terms[] = $deal['term_length'] > 0
				? sprintf(
					/* translators: 1: renewal price, 2: term length in months. */
					__( 'Renews at %1$s after %2$d months', 'zlaark-deals-pro' ),
					$deal['renewal_price'],
					(int) $deal['term_length']
				)
				: sprintf(
					/* translators: %s: renewal price. */
					__( 'Renews at %s', 'zlaark-deals-pro' ),
					$deal['renewal_price']
				);
		}
		if ( '' !== $deal['refund_window'] ) {
			$terms[] = $deal['refund_window'];
		}

		if ( empty( $terms ) && '' === $deal['verified_label'] ) {
			return;
		}
		?>
		<div class="zd-cterms">
			<?php foreach ( $terms as $line ) : ?>
				<span><?php echo esc_html( $line ); ?></span>
			<?php endforeach; ?>
			<?php if ( '' !== $deal['verified_label'] ) : ?>
				<span class="zd-cverified"><?php echo esc_html( $deal['verified_label'] ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/* ------------------------------------------- 08 browse by category */

	protected function section_categories( $s, $deals ) {
		$cats  = array();
		$total = max( 1, count( $deals ) );

		foreach ( $deals as $deal ) {
			foreach ( $deal['terms'] as $term ) {
				if ( ! isset( $cats[ $term->term_id ] ) ) {
					$cats[ $term->term_id ] = array(
						'name' => $term->name,
						'link' => get_term_link( $term ),
						'n'    => 0,
						'best' => null,
					);
				}
				$cats[ $term->term_id ]['n']++;

				// The headline number on a category tile should be the reason
				// to click it, not the size of the pile behind it.
				if ( null !== $deal['discount_pct'] ) {
					$pct = (int) $deal['discount_pct'];
					if ( null === $cats[ $term->term_id ]['best'] || $pct > $cats[ $term->term_id ]['best'] ) {
						$cats[ $term->term_id ]['best'] = $pct;
					}
				}
			}
		}

		if ( count( $cats ) < 2 ) {
			return;
		}

		uasort(
			$cats,
			function ( $a, $b ) {
				return $b['n'] - $a['n'];
			}
		);
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['cats_title'], __( 'Everything we rate, grouped', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-cats">
					<?php foreach ( $cats as $cat ) : ?>
						<?php $share = max( 6, min( 100, round( ( $cat['n'] / $total ) * 100 ) ) ); ?>
						<a class="zd-cats__tile zd-reveal" data-zd-reveal="rise"
							href="<?php echo esc_url( is_wp_error( $cat['link'] ) ? '#' : $cat['link'] ); ?>">
							<span class="zd-cats__top">
								<span class="zd-cats__name"><?php echo esc_html( $cat['name'] ); ?></span>
								<?php if ( null !== $cat['best'] ) : ?>
									<span class="zd-cats__best">
										<?php
										printf(
											/* translators: %d: best discount in this category. */
											esc_html__( 'up to %d%% off', 'zlaark-deals-pro' ),
											(int) $cat['best']
										);
										?>
									</span>
								<?php endif; ?>
							</span>

							<span class="zd-cats__bar" aria-hidden="true">
								<i style="width:<?php echo esc_attr( $share ); ?>%"></i>
							</span>

							<span class="zd-cats__n">
								<?php
								printf(
									/* translators: %d: number of deals in this category. */
									esc_html( _n( '%d deal', '%d deals', $cat['n'], 'zlaark-deals-pro' ) ),
									(int) $cat['n']
								);
								?>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* ------------------------------------------------- 09 ending soon */

	protected function section_expiring( $s, $deals ) {
		$within = max( 1, (int) $s['exp_days'] );
		$ending = array();

		foreach ( $deals as $deal ) {
			$days = Zlaark_Deals_Computed::days_until( $deal['expiry_date'] );
			if ( null !== $days && $days >= 0 && $days <= $within ) {
				$deal['_days'] = $days;
				$ending[]      = $deal;
			}
		}

		// Nothing expiring means no section. A countdown that is always on stops
		// being information and starts being decoration.
		if ( empty( $ending ) ) {
			return;
		}

		usort(
			$ending,
			function ( $a, $b ) {
				return $a['_days'] - $b['_days'];
			}
		);
		$ending = array_slice( $ending, 0, 5 );
		?>
		<section class="zd-home__sec zd-home__tint">
			<div class="zd-home__inner">
				<?php
				$this->section_head(
					$s['exp_title'],
					sprintf(
						/* translators: %d: number of deals ending. */
						_n( '%d offer closes shortly', '%d offers close shortly', count( $ending ), 'zlaark-deals-pro' ),
						count( $ending )
					)
				);
				?>

				<div class="zd-board zd-board--ember zd-reveal" data-zd-reveal="rise">
					<div class="zd-board__head">
						<b><?php esc_html_e( 'Closing soonest', 'zlaark-deals-pro' ); ?></b>
						<span class="zd-board__live"><?php esc_html_e( 'counting down', 'zlaark-deals-pro' ); ?></span>
					</div>

					<?php foreach ( $ending as $deal ) : ?>
						<div class="zd-board__row">
							<span class="zd-board__logo">
								<?php if ( $deal['image_id'] ) : ?>
									<?php echo wp_get_attachment_image( $deal['image_id'], 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<i aria-hidden="true"><?php echo esc_html( mb_substr( $deal['title'], 0, 2 ) ); ?></i>
								<?php endif; ?>
							</span>

							<span class="zd-board__name">
								<b><?php echo esc_html( $deal['title'] ); ?></b>
								<?php if ( '' !== $deal['price'] ) : ?>
									<span>
										<?php echo esc_html( $deal['price'] ); ?>
										<?php if ( '' !== $deal['old_price'] ) : ?>
											<s><?php echo esc_html( $deal['old_price'] ); ?></s>
										<?php endif; ?>
									</span>
								<?php endif; ?>
							</span>

							<span class="zd-board__count">
								<b><?php echo esc_html( 0 === $deal['_days'] ? __( 'today', 'zlaark-deals-pro' ) : (int) $deal['_days'] ); ?></b>
								<?php if ( 0 !== $deal['_days'] ) : ?>
									<i><?php echo esc_html( _n( 'day left', 'days left', (int) $deal['_days'], 'zlaark-deals-pro' ) ); ?></i>
								<?php endif; ?>
							</span>

							<span class="zd-board__go">
								<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-btn--sm' ); ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/* ------------------------------------------------ 10 how we test */

	protected function section_method( $s ) {
		$steps = is_array( $s['method_steps'] ) ? $s['method_steps'] : array();
		if ( empty( $steps ) ) {
			return;
		}
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['method_title'], __( 'The same four steps, every time', 'zlaark-deals-pro' ) ); ?>
				<ol class="zd-method">
					<?php foreach ( $steps as $i => $step ) : ?>
						<li class="zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
							<span class="zd-method__n"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
							<div>
								<h3><?php echo esc_html( $step['title'] ); ?></h3>
								<?php if ( '' !== $step['text'] ) : ?>
									<p><?php echo esc_html( $step['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
		<?php
	}

	/* ----------------------------------------------------- 11 about us */

	protected function section_about( $s ) {
		$people = is_array( $s['about_people'] ) ? $s['about_people'] : array();
		$people = array_values(
			array_filter(
				$people,
				function ( $p ) {
					return '' !== trim( (string) $p['name'] );
				}
			)
		);

		if ( empty( $people ) && '' === trim( (string) $s['about_text'] ) ) {
			return;
		}

		$url = ! empty( $s['about_cta_url']['url'] ) ? $s['about_cta_url']['url'] : '';
		?>
		<section class="zd-home__sec">
			<div class="zd-home__inner zd-about2">
				<div class="zd-about2__lead zd-reveal" data-zd-reveal="rise">
					<p class="zd-home__eyebrow"><?php esc_html_e( 'No outsourced reviews', 'zlaark-deals-pro' ); ?></p>
					<h2><?php echo esc_html( $s['about_title'] ); ?></h2>
					<?php if ( '' !== $s['about_text'] ) : ?>
						<p class="zd-about2__text"><?php echo esc_html( $s['about_text'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $s['about_cta_text'] && '' !== $url ) : ?>
						<a class="zd-btn zd-btn--ghost zd-about2__cta" href="<?php echo esc_url( $url ); ?>">
							<span class="zd-btn__label"><?php echo esc_html( $s['about_cta_text'] ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $people ) ) : ?>
					<ul class="zd-about2__people">
						<?php foreach ( $people as $i => $person ) : ?>
							<li class="zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $i; ?>">
								<span class="zd-about2__photo">
									<?php if ( ! empty( $person['photo']['url'] ) ) : ?>
										<img src="<?php echo esc_url( $person['photo']['url'] ); ?>"
											alt="<?php echo esc_attr( $person['name'] ); ?>" loading="lazy" />
									<?php else : ?>
										<i aria-hidden="true"><?php echo esc_html( mb_substr( $person['name'], 0, 1 ) ); ?></i>
									<?php endif; ?>
								</span>
								<span class="zd-about2__who">
									<b><?php echo esc_html( $person['name'] ); ?></b>
									<?php if ( '' !== $person['role'] ) : ?>
										<span class="zd-about2__role"><?php echo esc_html( $person['role'] ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $person['line'] ) : ?>
										<span class="zd-about2__line"><?php echo esc_html( $person['line'] ); ?></span>
									<?php endif; ?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/* -------------------------------------------------- 11 questions */

	protected function section_faq( $s ) {
		$items = is_array( $s['faq_items'] ) ? $s['faq_items'] : array();
		$items = array_values(
			array_filter(
				$items,
				function ( $i ) {
					return '' !== trim( (string) $i['q'] ) && '' !== trim( (string) $i['a'] );
				}
			)
		);

		if ( empty( $items ) ) {
			return;
		}
		?>
		<section class="zd-home__sec zd-home__tint">
			<div class="zd-home__inner">
				<?php $this->section_head( $s['faq_title'], __( 'Answered plainly', 'zlaark-deals-pro' ) ); ?>
				<div class="zd-faq">
					<?php foreach ( $items as $i => $item ) : ?>
						<details class="zd-faq__item zd-reveal" data-zd-reveal="rise"
							style="--zd-i:<?php echo (int) $i; ?>" <?php echo 0 === $i ? 'open' : ''; ?>>
							<summary><?php echo esc_html( $item['q'] ); ?></summary>
							<p><?php echo esc_html( $item['a'] ); ?></p>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php

		if ( 'yes' !== $s['faq_schema'] ) {
			return;
		}

		$graph = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array(),
		);

		foreach ( $items as $item ) {
			$graph['mainEntity'][] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $item['q'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $item['a'] ),
				),
			);
		}

		$json = wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		if ( $json ) {
			echo '<script type="application/ld+json">' . $json . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	protected function mini_card( $deal, $s, $index ) {
		?>
		<article class="zd-card zd-card--deal zd-reveal" data-zd-reveal="rise" style="--zd-i:<?php echo (int) $index; ?>">

			<?php if ( '' !== $deal['urgency_label'] ) : ?>
				<span class="zd-card__flag"><?php echo esc_html( $deal['urgency_label'] ); ?></span>
			<?php endif; ?>

			<?php $this->card_brand( $deal ); ?>
			<?php $this->card_price( $deal ); ?>

			<?php if ( '' !== $deal['coupon_code'] ) : ?>
				<div class="zd-coupon" data-zd-coupon="<?php echo esc_attr( $deal['coupon_code'] ); ?>">
					<span class="zd-coupon__code"><?php echo esc_html( $deal['coupon_code'] ); ?></span>
					<button type="button" class="zd-coupon__copy"
						data-zd-copied="<?php esc_attr_e( 'Copied', 'zlaark-deals-pro' ); ?>">
						<?php esc_html_e( 'Copy code', 'zlaark-deals-pro' ); ?>
					</button>
				</div>
			<?php endif; ?>

			<div class="zd-card__slot">
				<?php $this->card_body( $deal, 3 ); ?>
			</div>

			<?php $this->card_terms( $deal ); ?>

			<div class="zd-card__foot">
				<?php $this->render_cta( $deal, $s, 'zd-btn zd-btn--solid zd-home__block' ); ?>
				<?php if ( '' !== $deal['review_url'] ) : ?>
					<a class="zd-card__review" href="<?php echo esc_url( $deal['review_url'] ); ?>">
						<?php
						printf(
							/* translators: %s: deal title. */
							esc_html__( 'Read the full %s review', 'zlaark-deals-pro' ),
							esc_html( $deal['title'] )
						);
						?>
					</a>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}

}
