import React, { Component } from 'react';
import { BrowserRouter as Router, Route, Link, NavLink } from "react-router-dom";

export default class Footer extends Component {
  render() {
        return (
            <div>
                <div className="site-footer-legal">© 2020 <a href="javascript:void(0)">1To1</a></div>
            </div>
        );
    }
}

