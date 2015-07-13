<?php
/**
 * Joy Web Framework
 *
 * Copyright (c) 2008-2009 Netology Foundation (http://www.netology.org)
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL.
 */

/**
 * @package     Joy
 * @subpackage  File_Reader
 * @author      Hasan Ozgan <meddah@netology.org>
 * @copyright   2008-2009 Netology Foundation (http://www.netology.org)
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version     $Id: Ini.php 5 2010-01-12 15:26:24Z hasanozgan $
 * @link        http://joy.netology.org
 * @since       0.5
 */
class Joy_File_Reader_Ini extends Joy_File_Reader_Abstract
{
    public function toArray($hasSection=false)
    {
        return $this->parse_ini($this->_file, $hasSection);
    }

    public function parse_ini($f, $hasSection=false)
    {
        // if cannot open file, return false
        if (!is_file($f))
            return false;

        $ini = file($f);

        // to hold the categories, and within them the entries
        $cats = array();
        foreach ($ini as $i) {
            if ($hasSection) {
                if (@preg_match('/\[(.+)\]/', $i, $matches)) {
                    $last = $matches[1];
                } elseif (@preg_match('/(.+)=(.+)/', $i, $matches)) {
                    $cats[$last][trim($matches[1])] = trim($matches[2]);
                }
            }
            else if (@preg_match('/(.+)=(.+)/', $i, $matches)) {
                $cats[trim($matches[1])] = trim($matches[2]);
            }
        }
        
        return $cats;
    }
}
