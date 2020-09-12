<?php

namespace Dev4Press\Plugin\DebugPress\Main;

class Scope {
	private $scope;

	private $multisite;

	private $admin = false;
	private $network_admin = false;
	private $user_admin = false;
	private $blog_admin = false;

	private $frontend = false;

	private $blog_id;

	function __construct() {
		$this->multisite = is_multisite();
		$this->blog_id   = get_current_blog_id();

		if ( is_admin() ) {
			$this->admin = true;

			if ( is_blog_admin() ) {
				$this->blog_admin = true;
			} else if ( is_network_admin() ) {
				$this->network_admin = true;
			} else if ( is_user_admin() ) {
				$this->user_admin = true;
			}
		} else {
			$this->frontend = true;
		}

		if ( is_network_admin() ) {
			$this->scope = 'network';
		} else {
			$this->scope = 'blog';
		}
	}

	/** @return \Dev4Press\Plugin\DebugPress\Main\Scope */
	public static function instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Scope();
		}

		return $instance;
	}

	public function is_multisite() {
		return $this->multisite;
	}

	public function is_admin() {
		return $this->admin;
	}

	public function is_network_admin() {
		return $this->network_admin;
	}

	public function is_master_network_admin() {
		return ! $this->is_multisite() || $this->is_network_admin();
	}

	public function is_user_admin() {
		return $this->user_admin;
	}

	public function is_multisite_blog_admin( $blog_id = 0 ) {
		if ( ! $this->is_multisite() ) {
			return false;
		}

		$blog_id = absint( $blog_id );

		if ( $blog_id == 0 ) {
			return $this->blog_admin;
		} else {
			return $this->blog_admin && $this->blog_id = $blog_id;
		}
	}

	public function is_blog_admin( $blog_id = 0 ) {
		$blog_id = absint( $blog_id );

		if ( $blog_id == 0 ) {
			return $this->blog_admin;
		} else {
			return $this->blog_admin && $this->blog_id = $blog_id;
		}
	}

	public function is_frontend( $blog_id = 0 ) {
		$blog_id = absint( $blog_id );

		if ( $blog_id == 0 ) {
			return $this->frontend;
		} else {
			return $this->frontend && $this->blog_id = $blog_id;
		}
	}

	public function get_blog_id() {
		return $this->blog_id;
	}

	public function get_scope() {
		return $this->scope;
	}

	public function scope() {
		return array(
			'is_multisite'            => $this->is_multisite(),
			'is_frontend'             => $this->is_frontend(),
			'is_admin'                => $this->is_admin(),
			'is_blog_admin'           => $this->is_blog_admin(),
			'is_user_admin'           => $this->is_user_admin(),
			'is_network_admin'        => $this->is_network_admin(),
			'is_master_network_admin' => $this->is_master_network_admin(),
			'is_multisite_blog_admin' => $this->is_multisite_blog_admin(),
			'blog_id'                 => $this->get_blog_id(),
			'scope'                   => $this->get_scope()
		);
	}
}
