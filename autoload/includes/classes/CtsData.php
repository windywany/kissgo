<?php
/**
 * 数据提供器可以提供的结果
 */
class CtsData implements Iterator {
    private $data = array ();
    private $pos = 0;
    private $total = 0;
    private $countTotal = 0;
    private $per = 10;
    public function __construct($data = array(), $countTotal = 0, $per = 0) {
        $this->data = $data;
        if (is_array ( $data )) {
            $this->total = count ( $data );
        } else if ($data instanceof ResultCursor) {
            $this->total = $data->size ();
        }
        $this->countTotal = $countTotal;
        $this->per = $per <= 0 ? 1 : intval ( $per );
    }
    
    /**
     * 取用于ctv标签的数据
     *
     * @return mixed
     */
    public function assign() {
        if (is_array ( $this->data ) || $this->data instanceof ResultCursor) {
            return empty ( $this->data ) ? array () : $this->data [0];
        } else {
            return $this->data;
        }
    }
    public function current() {
        if (is_array ( $this->data ) || $this->data instanceof ResultCursor) {
            return $this->data [$this->pos];
        }
        return null;
    }
    public function next() {
        $this->pos ++;
    }
    public function key() {
        return $this->pos;
    }
    public function valid() {
        return $this->pos < $this->total;
    }
    public function rewind() {
        $this->pos = 0;
    }
    
    /**
     * 绘制分页
     * @param string $render
     * @param array $options
     * @return array
     */
    public final function onPagingRender($render, $options) {
        global $_current_page;
        $_current_page = $_current_page == null ? 1 : $_current_page;
        $url_info = Request::parseURL ();
        if (is_null ( $this->countTotal ) && $this->data instanceof ResultCursor) {
            $this->countTotal = count ( $this->data );
        }
        if ($url_info && $this->countTotal > 0) {
            $paging = array ('orgin' => $url_info ['orgin'], 'prefix' => $url_info ['prefix'], 'current' => $_current_page, 'total' => $this->countTotal, 'limit' => $this->per, 'ext' => $url_info ['suffix'] );
            $paging_data = apply_filter ( 'on_render_paging_by_' . $render, array (), $paging, $options );
            if (empty ( $paging_data )) {
                $paging_data = $this->getPageInfo ( $paging, $options );
            } else if (is_array ( $paging_data )) {
                $paging_data = array_merge2 ( array ('total' => ceil ( $this->countTotal / $this->per ), 'ctotal' => $this->countTotal, 'first' => '#', 'prev' => '#', 'next' => '#', 'last' => '#' ), $paging_data );
            }
            return $paging_data;
        }
    }
    
    /**
     * 取分页
     * @param $paging
     */
    private function getPageInfo($paging, $args) {
        $url = safe_url ( $paging ['prefix'] );
        $cur = $paging ['current'];
        $total = $paging ['total'];
        $per = $paging ['limit'];
        $ext = $paging ['ext'];
        $tp = ceil ( $total / $per ); // 一共有多少页
        $pager = array ();
        if ($tp < 2) {
            return $pager;
        }
        $pager ['total'] = $tp;
        $pager ['ctotal'] = $total;
        if ($cur == 1) { // 当前在第一页
            $pager ['first'] = '#';
            $pager ['prev'] = '#';
        } else {
            $pager ['first'] = $args ['orgin'];
            $pager ['prev'] = $cur == 2 ? $args ['orgin'] : $url . ($cur - 1) . $ext;
        }
        // 向前后各多少页
        $pp = isset ( $args ['pp'] ) ? intval ( $args ['pp'] ) : 10;
        $sp = $pp % 2 == 0 ? $pp / 2 : ($pp - 1) / 2;
        if ($cur <= $sp) {
            $start = 1;
            $end = $pp;
            $end = $end > $tp ? $tp : $end;
        } else {
            $start = $cur - $sp;
            $end = $cur + $sp;
            if ($pp % 2 == 0) {
                $end -= 1;
            }
            if ($end >= $tp) {
                $start -= ($end - $tp);
                $start > 0 or $start = 1;
                $end = $tp;
            }
        }
        for($i = $start; $i <= $end; $i ++) {
            if ($i == $cur) {
                $pager [$i] = '#';
            } else if ($i == 1) {
                $pager [$i] = $args ['orgin'];
            } else {
                $pager [$i] = $url . $i . $ext;
            }
        }
        if ($cur == $tp) {
            $pager ['next'] = '#';
            $pager ['last'] = '#';
        } else {
            $pager ['next'] = $url . ($cur + 1) . $ext;
            $pager ['last'] = $url . $tp . $ext;
        }
        return $pager;
    }
}