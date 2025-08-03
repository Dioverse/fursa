"use client"
import React from 'react';
import CategoryMenu from './CategoryMenu';
function NavItem() {
    return (
        <div className="container-fluid">
            <nav>
                <ul className="parent-nav">
                    <li className="parent has-dropdown">
                        <div className="logo-search-category-wrapper">
                            <div className="category-search-wrapper">
                                <div className="category-btn category-hover-header">
                                    <img className="parent" src="/assets/images/icons/bar-1.svg" alt="icons" />
                                    <span>Categories</span>
                                    <CategoryMenu />
                                </div>
                            </div>
                        </div>
                    </li>
                    <li className="parent has-dropdown">
                        <a className="nav-link" href="#">
                            Home
                        </a>
                    </li>
                    <li className="parent with-megamenu">
                        <a href="#">Shop</a>
                    </li>

                    <li className="parent">
                        <a href="/about">About Us</a>
                    </li>

                    <li className="parent has-dropdown">
                        <a className="nav-link" href="#">
                            Blog
                        </a>
                        {/* <ul className="submenu">
                            <li>
                                <a className="sub-b" href="/blog">
                                    Blog
                                </a>
                            </li>
                            <li>
                                <a
                                    className="sub-b"
                                    href="/blog-list-left-sidebar"
                                >
                                    Blog List Left Sidebar
                                </a>
                            </li>
                            <li>
                                <a
                                    className="sub-b"
                                    href="/blog-list-right-sidebar"
                                >

                                    Blog List Right Sidebar
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/blog/details-profitable-business-makes-your-profit">
                                    Blog Details
                                </a>
                            </li>
                        </ul> */}
                    </li>

                    <li className="parent">
                        <a href="/contact">Contact Us</a>
                    </li>

                    <li className="parent">
                        <a href="/contact">Buy for Business</a>
                    </li>

                    <li className="parent">
                        <a href="/contact">Know your Engine</a>
                    </li>
                    
                    {/* <li className="parent has-dropdown">
                        <a className="nav-link" href="#">
                            Vendors
                        </a>
                        <ul className="submenu">
                            <li>
                                <a className="sub-b" href="/vendor-list">
                                    Vendor List
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/vendor-grid">
                                    Vendor Grid
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/vendor-details">
                                    Vendor Details
                                </a>
                            </li>
                        </ul>
                    </li> */}
                    {/* <li className="parent has-dropdown">
                        <a className="nav-link" href="#">
                            Pages
                        </a>
                        <ul className="submenu">
                            <li>
                                <a className="sub-b" href="/dashboard">
                                    Dashboard
                                    <span className="badge">( New )</span>
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/about">
                                    About
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/store">
                                    Store
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/faq">
                                    Faq's
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/invoice">
                                    Invoice
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/contact">
                                    Contact
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/register">
                                    Register
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/login">
                                    Login
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/privacy-policy">
                                    Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/cookies-policy">
                                    Cookies Policy
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/terms-condition">
                                    Terms &amp; Condition
                                </a>
                            </li>
                            <li>
                                <a className="sub-b" href="/404">
                                    Error
                                </a>
                            </li>
                        </ul>
                    </li> */}
                    
                    {/* <li className="parents">
                        <a target='_blank' href="/dashboard">
                            Dashboard
                            <span className="badge">New</span>
                        </a>
                    </li> */}
                    
                </ul>
            </nav>
        </div>
    );
}

export default NavItem;